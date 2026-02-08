<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Widgets\Concerns\IsWidget;
use App\Models\Contact;
use App\Models\Equipment;
use App\Models\Event;
use App\Models\OperatingSession;
use App\Models\Station;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

/**
 * ListWidget - Displays scrollable lists of data.
 *
 * Supports three list types:
 * - recent_contacts: Last N contacts with time, callsign, band, mode, operator
 * - active_stations: Currently active stations with operator, band, status
 * - equipment_status: Equipment usage with name, status, assigned_to
 *
 * Config structure:
 * [
 *   'list_type' => 'recent_contacts|active_stations|equipment_status'
 * ]
 *
 * Size variants:
 * - normal: 15 items, standard text
 * - tv: 10 items, larger text and spacing
 */
class ListWidget extends Component
{
    use IsWidget;

    /**
     * Fetch the list data for this widget.
     *
     * Returns an array of items formatted for display.
     */
    public function getData(): array
    {
        return Cache::remember(
            $this->cacheKey(),
            3,
            fn () => $this->fetchListData()
        );
    }

    /**
     * Define Livewire event listeners for this widget.
     *
     * Different list types listen to different events:
     * - recent_contacts: ContactLogged (immediate update)
     * - active_stations: StationStatusChanged (immediate update)
     * - equipment_status: Polling (no event listeners)
     */
    public function getWidgetListeners(): array
    {
        $listType = $this->config['list_type'] ?? 'recent_contacts';

        return match ($listType) {
            'recent_contacts' => [
                'echo:contacts,ContactLogged' => 'handleUpdate',
            ],
            'active_stations' => [
                'echo:stations,StationStatusChanged' => 'handleUpdate',
            ],
            default => [],
        };
    }

    /**
     * Fetch the appropriate list data based on config.
     */
    protected function fetchListData(): array
    {
        $listType = $this->config['list_type'] ?? 'recent_contacts';
        $event = Event::active()->first();

        if (! $event || ! $event->eventConfiguration) {
            return [];
        }

        return match ($listType) {
            'recent_contacts' => $this->getRecentContacts($event),
            'active_stations' => $this->getActiveStations($event),
            'equipment_status' => $this->getEquipmentStatus($event),
            default => [],
        };
    }

    /**
     * Get recent contacts list.
     *
     * Format: Time ago, callsign, band, mode, operator
     */
    protected function getRecentContacts(Event $event): array
    {
        $limit = $this->isTvSize() ? 10 : 15;

        return Contact::query()
            ->where('event_configuration_id', $event->eventConfiguration->id)
            ->with(['band', 'mode', 'operatingSession.operator', 'operatingSession.station'])
            ->notDuplicate()
            ->chronological()
            ->limit($limit)
            ->get()
            ->map(function (Contact $contact) {
                return [
                    'type' => 'recent_contact',
                    'time_ago' => $this->formatTimeAgo($contact->qso_time),
                    'callsign' => $contact->callsign,
                    'band' => $contact->band?->name ?? 'Unknown',
                    'mode' => $contact->mode?->name ?? 'Unknown',
                    'operator' => $contact->operatingSession?->operator?->callsign ?? 'Unknown',
                    'station' => $contact->operatingSession?->station?->name ?? 'Unknown',
                ];
            })
            ->all();
    }

    /**
     * Get active stations list.
     *
     * Format: Station name, operator, current band, status
     */
    protected function getActiveStations(Event $event): array
    {
        $limit = $this->isTvSize() ? 10 : 15;

        $activeSessions = OperatingSession::query()
            ->whereHas('station', function ($query) use ($event) {
                $query->where('event_configuration_id', $event->eventConfiguration->id);
            })
            ->with(['station', 'operator', 'band', 'mode'])
            ->active()
            ->orderBy('start_time', 'desc')
            ->limit($limit)
            ->get();

        return $activeSessions->map(function (OperatingSession $session) {
            return [
                'type' => 'active_station',
                'station_name' => $session->station?->name ?? 'Unknown',
                'operator_name' => $session->operator?->callsign ?? 'Unknown',
                'band' => $session->band?->name ?? 'Unknown',
                'mode' => $session->mode?->name ?? 'Unknown',
                'status' => 'Active',
                'status_color' => 'success',
            ];
        })->all();
    }

    /**
     * Get equipment status list.
     *
     * Format: Equipment name, status, assigned to
     */
    protected function getEquipmentStatus(Event $event): array
    {
        $limit = $this->isTvSize() ? 10 : 15;

        $commitments = \App\Models\EquipmentEvent::query()
            ->where('event_id', $event->id)
            ->with(['equipment', 'station'])
            ->whereIn('status', ['committed', 'delivered', 'in_use'])
            ->get()
            ->sortBy(function ($commitment) {
                // Sort by status priority: in_use, delivered, committed
                return match ($commitment->status) {
                    'in_use' => 1,
                    'delivered' => 2,
                    'committed' => 3,
                    default => 99,
                };
            })
            ->take($limit);

        return $commitments->map(function (\App\Models\EquipmentEvent $commitment) {
            $equipment = $commitment->equipment;
            $equipmentName = $equipment
                ? trim("{$equipment->make} {$equipment->model}")
                : 'Unknown Equipment';

            return [
                'type' => 'equipment',
                'equipment_name' => $equipmentName,
                'status' => ucfirst($commitment->status),
                'status_color' => $this->getStatusColor($commitment->status),
                'assigned_to' => $commitment->station?->name ?? 'Unassigned',
            ];
        })->values()->all();
    }

    /**
     * Format time difference in human-readable form.
     *
     * Examples: "5m ago", "1h ago", "2d ago"
     */
    protected function formatTimeAgo(Carbon $time): string
    {
        $now = appNow();
        $diff = $time->diffInSeconds($now);

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);

            return "{$minutes}m ago";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);

            return "{$hours}h ago";
        } else {
            $days = floor($diff / 86400);

            return "{$days}d ago";
        }
    }

    /**
     * Get color class for equipment status badge.
     */
    protected function getStatusColor(string $status): string
    {
        return match ($status) {
            'in_use' => 'success',
            'delivered' => 'info',
            'committed' => 'warning',
            'cancelled', 'lost', 'damaged' => 'error',
            'returned' => 'neutral',
            default => 'neutral',
        };
    }

    public function render()
    {
        $listType = $this->config['list_type'] ?? 'recent_contacts';

        return view('livewire.dashboard.widgets.list-widget', [
            'data' => $this->getData(),
            'listType' => $listType,
        ]);
    }
}
