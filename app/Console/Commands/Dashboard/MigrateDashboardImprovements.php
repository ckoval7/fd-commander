<?php

namespace App\Console\Commands\Dashboard;

use App\Models\Dashboard;
use Illuminate\Console\Command;

class MigrateDashboardImprovements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:migrate-improvements
                            {--dry-run : Show what would be changed without making changes}
                            {--user= : Only migrate dashboards for specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing dashboards to use the new dashboard improvements configuration';

    protected int $dashboardsUpdated = 0;

    protected int $widgetsUpdated = 0;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Dashboard Improvements Migration');
        $this->newLine();

        $isDryRun = $this->option('dry-run');
        $userId = $this->option('user');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be saved');
            $this->newLine();
        }

        // Get dashboards to migrate
        $query = Dashboard::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $dashboards = $query->get();

        if ($dashboards->isEmpty()) {
            $this->error('No dashboards found to migrate.');

            return self::FAILURE;
        }

        $this->info("Found {$dashboards->count()} dashboard(s) to migrate");
        $this->newLine();

        // Process each dashboard
        foreach ($dashboards as $dashboard) {
            $this->migrateDashboard($dashboard, $isDryRun);
        }

        $this->newLine();
        $this->info('✅ Migration Complete!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Dashboards Updated', $this->dashboardsUpdated],
                ['Widgets Updated', $this->widgetsUpdated],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        }

        return self::SUCCESS;
    }

    protected function migrateDashboard(Dashboard $dashboard, bool $isDryRun): void
    {
        $this->line("📊 Migrating dashboard: {$dashboard->title} (ID: {$dashboard->id})");

        $config = $dashboard->config;
        $widgets = $config['widgets'] ?? $config ?? [];

        if (empty($widgets)) {
            $this->warn('  ⚠️  No widgets found, skipping');

            return;
        }

        $updatedWidgets = [];
        $hasChanges = false;

        // Define the new widget order
        $newOrderMap = [
            'timer' => 0,
            'stat_card_qso_rate' => 1,
            'stat_card_total_score' => 2,
            'progress_bar' => 3,
            'chart' => 4,
            'list_widget_recent' => 5,
            'list_widget_active' => 6,
        ];

        foreach ($widgets as $widget) {
            $updated = $this->migrateWidget($widget);

            if ($updated !== $widget) {
                $hasChanges = true;
                $this->widgetsUpdated++;
            }

            // Only keep widgets that aren't marked for removal
            if ($updated !== null) {
                $updatedWidgets[] = $updated;
            }
        }

        // Reorder widgets based on type
        usort($updatedWidgets, function ($a, $b) use ($newOrderMap) {
            $orderA = $this->getWidgetNewOrder($a, $newOrderMap);
            $orderB = $this->getWidgetNewOrder($b, $newOrderMap);

            return $orderA <=> $orderB;
        });

        // Update order property
        foreach ($updatedWidgets as $index => $widget) {
            $updatedWidgets[$index]['order'] = $index;
        }

        if ($hasChanges || count($updatedWidgets) !== count($widgets)) {
            $this->dashboardsUpdated++;

            if (! $isDryRun) {
                // Update the dashboard config
                if (isset($config['widgets'])) {
                    $config['widgets'] = $updatedWidgets;
                } else {
                    $config = $updatedWidgets;
                }

                $dashboard->update(['config' => $config]);
            }

            $this->info("  ✓ Updated {$dashboard->title}");
        } else {
            $this->line('  → No changes needed');
        }
    }

    protected function migrateWidget(array $widget): ?array
    {
        $type = $widget['type'] ?? null;
        $changes = [];

        // Migrate chart widgets
        if ($type === 'chart') {
            $config = $widget['config'] ?? [];

            // Update chart_type to 'line' if it's 'bar'
            if (($config['chart_type'] ?? null) === 'bar') {
                $config['chart_type'] = 'line';
                $changes[] = 'chart_type: bar → line';
            }

            // Update time_range to 'last_12_hours' if it's 'event' or 'all_time'
            if (in_array($config['time_range'] ?? null, ['event', 'all_time'])) {
                $config['time_range'] = 'last_12_hours';
                $changes[] = 'time_range: '.$widget['config']['time_range'].' → last_12_hours';
            }

            if (! empty($changes)) {
                $widget['config'] = $config;
                $this->line('    • Chart: '.implode(', ', $changes));
            }
        }

        // Migrate stat cards
        if ($type === 'stat_card') {
            $config = $widget['config'] ?? [];

            // Add comparison settings if not present
            if (! isset($config['show_comparison'])) {
                $config['show_comparison'] = true;
                $changes[] = 'enabled comparison';
            }

            if (! isset($config['comparison_interval'])) {
                $config['comparison_interval'] = '1h';
                $changes[] = 'set comparison interval to 1h';
            }

            // Update old metric names to new ones
            $metricMap = [
                'qso_count' => 'qso_count', // Keep as is
                'total_score' => 'total_score', // Keep as is
            ];

            if (isset($config['metric']) && isset($metricMap[$config['metric']])) {
                $oldMetric = $config['metric'];
                $config['metric'] = $metricMap[$oldMetric];
                if ($oldMetric !== $config['metric']) {
                    $changes[] = "metric: {$oldMetric} → {$config['metric']}";
                }
            }

            if (! empty($changes)) {
                $widget['config'] = $config;
                $this->line('    • Stat Card ('.$config['metric'].'): '.implode(', ', $changes));
            }
        }

        // Remove Activity Feed widgets (marked as deprecated)
        if ($type === 'feed' || $type === 'activity_feed') {
            $listType = $widget['config']['feed_type'] ?? null;

            // Only remove if it's showing stale data (all_activity or contacts_only with old data)
            if (in_array($listType, ['all_activity', 'contacts_only'])) {
                $this->warn('    ⨯ Removed Activity Feed widget (showing stale data)');

                return null; // Remove this widget
            }
        }

        return $widget;
    }

    protected function getWidgetNewOrder(array $widget, array $newOrderMap): int
    {
        $type = $widget['type'] ?? 'unknown';
        $config = $widget['config'] ?? [];

        // Map widget to order key
        $orderKey = match ($type) {
            'timer' => 'timer',
            'stat_card' => match ($config['metric'] ?? '') {
                'qso_per_hour', 'contacts_last_hour', 'avg_qso_rate_4h' => 'stat_card_qso_rate',
                'total_score' => 'stat_card_total_score',
                default => 'stat_card_other',
            },
            'progress_bar' => 'progress_bar',
            'chart' => 'chart',
            'list_widget' => match ($config['list_type'] ?? '') {
                'recent_contacts' => 'list_widget_recent',
                'active_stations' => 'list_widget_active',
                default => 'list_widget_other',
            },
            default => 'other',
        };

        return $newOrderMap[$orderKey] ?? 999;
    }
}
