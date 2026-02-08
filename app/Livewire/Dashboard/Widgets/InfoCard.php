<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Widgets\Concerns\IsWidget;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

/**
 * InfoCard Widget
 *
 * Displays event information in a simple key-value format.
 * Shows event name, location, operating class, and call sign.
 *
 * Supports size variants (normal/tv) with larger text for TV displays.
 * Gracefully handles missing event or configuration data.
 */
class InfoCard extends Component
{
    use IsWidget;

    /**
     * Fetch the event information data for this widget.
     *
     * Queries the active event and its configuration to gather:
     * - Event name
     * - Location (from event configuration)
     * - Operating class
     * - Call sign
     *
     * Returns graceful defaults if data is missing.
     */
    public function getData(): array
    {
        if ($this->shouldCache()) {
            return Cache::remember(
                $this->cacheKey(),
                now()->addSeconds(60),
                fn () => $this->gatherEventInfo()
            );
        }

        return $this->gatherEventInfo();
    }

    /**
     * Define Livewire event listeners for this widget.
     *
     * Returns empty array - event info rarely changes during active events.
     */
    public function getWidgetListeners(): array
    {
        return [];
    }

    /**
     * Gather event information from active event and configuration.
     *
     * @return array<string, string>
     */
    protected function gatherEventInfo(): array
    {
        $event = Event::active()->first();

        if (! $event || ! $event->eventConfiguration) {
            return $this->emptyInfo();
        }

        $config = $event->eventConfiguration;

        return [
            'event_name' => $event->name ?? 'N/A',
            'location' => $config->section?->name ?? 'N/A',
            'operating_class' => $config->operatingClass?->name ?? 'N/A',
            'call_sign' => $config->callsign ?? 'N/A',
        ];
    }

    /**
     * Return empty info data structure.
     *
     * @return array<string, string>
     */
    protected function emptyInfo(): array
    {
        return [
            'event_name' => 'N/A',
            'location' => 'N/A',
            'operating_class' => 'N/A',
            'call_sign' => 'N/A',
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.widgets.info-card', [
            'data' => $this->getData(),
        ]);
    }
}
