<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Widgets\Concerns\IsWidget;
use App\Models\Contact;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

/**
 * StatCard Widget
 *
 * Displays a single metric from the active event in a card format.
 * Supports multiple metric types and size variants (normal/tv).
 *
 * Metric Types:
 * - total_score: Sum of all QSO points for active event
 * - qso_count: Count of contacts for active event
 * - sections_worked: Count of unique sections worked
 * - operators_count: Count of unique operators
 *
 * Config structure:
 * [
 *   'metric' => 'total_score|qso_count|sections_worked|operators_count'
 * ]
 */
class StatCard extends Component
{
    use IsWidget;

    /**
     * Fetch the metric data for this widget.
     *
     * Returns an array with the metric value, label, and icon.
     */
    public function getData(): array
    {
        if ($this->shouldCache()) {
            return Cache::remember(
                $this->cacheKey(),
                now()->addSeconds(3),
                fn () => $this->calculateMetric()
            );
        }

        return $this->calculateMetric();
    }

    /**
     * Define Livewire event listeners for this widget.
     *
     * Returns empty array for now - batched updates handled via polling.
     */
    public function getWidgetListeners(): array
    {
        return [];
    }

    /**
     * Calculate the metric value based on widget config.
     */
    protected function calculateMetric(): array
    {
        $metric = $this->config['metric'] ?? 'qso_count';
        $event = Event::active()->first();

        if (! $event || ! $event->eventConfiguration) {
            return $this->emptyMetric($metric);
        }

        return match ($metric) {
            'total_score' => $this->getTotalScore($event),
            'qso_count' => $this->getQsoCount($event),
            'sections_worked' => $this->getSectionsWorked($event),
            'operators_count' => $this->getOperatorsCount($event),
            default => $this->emptyMetric($metric),
        };
    }

    /**
     * Get total score metric.
     */
    protected function getTotalScore(Event $event): array
    {
        $score = Contact::query()
            ->where('event_configuration_id', $event->eventConfiguration->id)
            ->notDuplicate()
            ->sum('points') ?? 0;

        return [
            'value' => number_format($score),
            'label' => 'Total Score',
            'icon' => 'o-trophy',
            'color' => 'text-success',
        ];
    }

    /**
     * Get QSO count metric.
     */
    protected function getQsoCount(Event $event): array
    {
        $count = Contact::query()
            ->where('event_configuration_id', $event->eventConfiguration->id)
            ->notDuplicate()
            ->count();

        return [
            'value' => number_format($count),
            'label' => 'QSOs',
            'icon' => 'o-chat-bubble-left-right',
            'color' => 'text-primary',
        ];
    }

    /**
     * Get sections worked metric.
     */
    protected function getSectionsWorked(Event $event): array
    {
        $count = Contact::query()
            ->where('event_configuration_id', $event->eventConfiguration->id)
            ->notDuplicate()
            ->whereNotNull('section_id')
            ->distinct('section_id')
            ->count('section_id');

        return [
            'value' => number_format($count),
            'label' => 'Sections',
            'icon' => 'o-map',
            'color' => 'text-info',
        ];
    }

    /**
     * Get operators count metric.
     */
    protected function getOperatorsCount(Event $event): array
    {
        $count = Contact::query()
            ->where('event_configuration_id', $event->eventConfiguration->id)
            ->join('operating_sessions', 'contacts.operating_session_id', '=', 'operating_sessions.id')
            ->distinct('operating_sessions.operator_user_id')
            ->count('operating_sessions.operator_user_id');

        return [
            'value' => number_format($count),
            'label' => 'Operators',
            'icon' => 'o-users',
            'color' => 'text-warning',
        ];
    }

    /**
     * Return empty metric data.
     */
    protected function emptyMetric(string $metric): array
    {
        $labels = [
            'total_score' => ['Total Score', 'o-trophy', 'text-success'],
            'qso_count' => ['QSOs', 'o-chat-bubble-left-right', 'text-primary'],
            'sections_worked' => ['Sections', 'o-map', 'text-info'],
            'operators_count' => ['Operators', 'o-users', 'text-warning'],
        ];

        [$label, $icon, $color] = $labels[$metric] ?? ['Unknown', 'o-question-mark-circle', 'text-base-content'];

        return [
            'value' => '0',
            'label' => $label,
            'icon' => $icon,
            'color' => $color,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.widgets.stat-card', [
            'data' => $this->getData(),
        ]);
    }
}
