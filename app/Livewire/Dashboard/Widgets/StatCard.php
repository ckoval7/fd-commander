<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Widgets\Concerns\IsWidget;
use App\Models\Contact;
use App\Models\Event;
use App\Models\EventBonus;
use App\Services\EventContextService;
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
        $service = app(EventContextService::class);
        $event = $service->getContextEvent();

        if ($this->shouldCache()) {
            $data = Cache::remember(
                $this->cacheKey(),
                now()->addSeconds(3),
                fn () => $this->calculateMetric()
            );
        } else {
            $data = $this->calculateMetric();
        }

        // Add comparison data if enabled
        if ($event) {
            $data = $this->addComparisonData($data, $event);
        }

        return $data;
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
        $service = app(EventContextService::class);
        $event = $service->getContextEvent();

        if (! $event || ! $event->eventConfiguration) {
            return $this->emptyMetric($metric);
        }

        return match ($metric) {
            'total_score' => $this->getTotalScore($event),
            'qso_count' => $this->getQsoCount($event),
            'sections_worked' => $this->getSectionsWorked($event),
            'operators_count' => $this->getOperatorsCount($event),
            'qso_per_hour' => $this->getQsoPerHour($event),
            'avg_qso_rate_4h' => $this->getAvgQsoRate4h($event),
            'contacts_last_hour' => $this->getContactsLastHour($event),
            'hours_remaining' => $this->getHoursRemaining($event),
            'bonus_points_earned' => $this->getBonusPointsEarned($event),
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
            'last_updated_at' => appNow(),
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
            'last_updated_at' => appNow(),
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
            'last_updated_at' => appNow(),
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
            'last_updated_at' => appNow(),
        ];
    }

    /**
     * Get 4-hour rolling average QSO rate metric.
     */
    protected function getAvgQsoRate4h(Event $event): array
    {
        $fourHoursAgo = appNow()->subHours(4);

        $count = Contact::query()
            ->where('event_configuration_id', $event->eventConfiguration->id)
            ->notDuplicate()
            ->where('qso_time', '>=', $fourHoursAgo)
            ->count();

        // Calculate hourly average: (count / 4)
        $avgRate = $count / 4;

        return [
            'value' => number_format($avgRate, 1),
            'label' => 'Avg QSO Rate (4h)',
            'icon' => 'o-chart-bar',
            'color' => 'text-info',
            'last_updated_at' => appNow(),
        ];
    }

    /**
     * Get contacts in last hour metric.
     */
    protected function getContactsLastHour(Event $event): array
    {
        $oneHourAgo = appNow()->subHour();

        $count = Contact::query()
            ->where('event_configuration_id', $event->eventConfiguration->id)
            ->notDuplicate()
            ->where('qso_time', '>=', $oneHourAgo)
            ->count();

        return [
            'value' => number_format($count),
            'label' => 'Contacts Last Hour',
            'icon' => 'o-clock',
            'color' => 'text-success',
            'last_updated_at' => appNow(),
        ];
    }

    /**
     * Get hours remaining in event metric.
     */
    protected function getHoursRemaining(Event $event): array
    {
        // Calculate hours remaining
        // If end_time is in the future, this returns positive; if past, returns negative
        $hoursRemaining = appNow()->diffInHours($event->end_time, false);

        // If negative (event ended), return 0
        if ($hoursRemaining < 0) {
            $hoursRemaining = 0;
        }

        return [
            'value' => number_format($hoursRemaining),
            'label' => 'Hours Remaining',
            'icon' => 'o-clock',
            'color' => 'text-warning',
            'last_updated_at' => appNow(),
        ];
    }

    /**
     * Get accumulated bonus points metric.
     */
    protected function getBonusPointsEarned(Event $event): array
    {
        $bonusPoints = EventBonus::query()
            ->where('event_configuration_id', $event->eventConfiguration->id)
            ->sum('calculated_points') ?? 0;

        return [
            'value' => number_format($bonusPoints),
            'label' => 'Bonus Points',
            'icon' => 'o-star',
            'color' => 'text-accent',
            'last_updated_at' => appNow(),
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
            'avg_qso_rate_4h' => ['Avg QSO Rate (4h)', 'o-chart-bar', 'text-info'],
            'contacts_last_hour' => ['Contacts Last Hour', 'o-clock', 'text-success'],
            'hours_remaining' => ['Hours Remaining', 'o-clock', 'text-warning'],
            'bonus_points_earned' => ['Bonus Points', 'o-star', 'text-accent'],
        ];

        [$label, $icon, $color] = $labels[$metric] ?? ['Unknown', 'o-question-mark-circle', 'text-base-content'];

        return [
            'value' => '0',
            'label' => $label,
            'icon' => $icon,
            'color' => $color,
            'last_updated_at' => appNow(),
        ];
    }

    /**
     * Add comparison data to the metric if enabled.
     *
     * Compares current value with historical snapshot from cache.
     * Calculates change amount, percentage, and trend direction.
     */
    protected function addComparisonData(array $current, Event $event): array
    {
        // Check if comparison is enabled
        $showComparison = $this->config['show_comparison'] ?? true;

        if (! $showComparison) {
            return $current;
        }

        // Check if event has configuration
        if (! $event->eventConfiguration) {
            return $current;
        }

        // Get comparison interval (default to 1h)
        $interval = $this->config['comparison_interval'] ?? '1h';

        // Generate historical cache key
        $configHash = md5(json_encode($this->config));
        $historicalKey = "dashboard:widget:StatCard:{$configHash}:{$event->eventConfiguration->id}:history:{$interval}";

        // Get previous value from cache
        $previousValue = Cache::get($historicalKey);

        // Extract numeric value from current data (remove formatting)
        $currentNumeric = (float) str_replace(',', '', $current['value']);

        // If no previous value, store current and return without comparison
        if ($previousValue === null) {
            // Set TTL based on interval
            $ttl = match ($interval) {
                '1h' => now()->addHours(1)->addHour(), // 1h + 1h buffer
                '4h' => now()->addHours(4)->addHour(), // 4h + 1h buffer
                default => now()->addHours(2),
            };

            Cache::put($historicalKey, $currentNumeric, $ttl);

            return $current;
        }

        // Calculate comparison metrics
        $changeAmount = $currentNumeric - $previousValue;
        $changePercentage = $previousValue > 0
            ? (($currentNumeric - $previousValue) / $previousValue) * 100
            : 0;

        // Determine trend
        $trend = match (true) {
            $changeAmount > 0 => 'up',
            $changeAmount < 0 => 'down',
            default => 'stable',
        };

        // Format comparison label
        $comparisonLabel = match ($interval) {
            '1h' => 'vs 1h ago',
            '4h' => 'vs 4h ago',
            default => 'vs earlier',
        };

        // Store current value as new historical snapshot
        $ttl = match ($interval) {
            '1h' => now()->addHours(1)->addHour(),
            '4h' => now()->addHours(4)->addHour(),
            default => now()->addHours(2),
        };
        Cache::put($historicalKey, $currentNumeric, $ttl);

        // Add comparison fields to data
        return array_merge($current, [
            'previous_value' => $previousValue,
            'change_amount' => $changeAmount,
            'change_percentage' => round($changePercentage, 1),
            'trend' => $trend,
            'comparison_label' => $comparisonLabel,
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.widgets.stat-card', [
            'data' => $this->getData(),
        ]);
    }
}
