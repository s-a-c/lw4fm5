<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Theme\ThemeTelescopeMetrics;
use Illuminate\Console\Command;

/**
 * Artisan command to display theme event metrics from Telescope (T027d, FR-099).
 */
final class ThemeTelescopeMetricsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:telescope-metrics
                            {--event-type= : Filter by specific event type (theme_changed, validation_corrected, preview_interaction)}
                            {--hours=24 : Number of hours to look back}
                            {--aggregated : Show aggregated metrics across all event types}
                            {--performance : Show performance metrics from ThemePerformanceTracker}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display theme event metrics from Telescope (p50, p95, p99 latencies, event counts, error rates)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $eventType = $this->option('event-type');
        $aggregated = $this->option('aggregated');
        $performance = $this->option('performance');

        if ($performance) {
            return $this->displayPerformanceMetrics();
        }

        if ($aggregated) {
            return $this->displayAggregatedMetrics($hours);
        }

        if ($eventType) {
            return $this->displayEventMetrics($eventType, $hours);
        }

        // Default: show aggregated metrics
        return $this->displayAggregatedMetrics($hours);
    }

    /**
     * Display metrics for a specific event type.
     */
    private function displayEventMetrics(string $eventType, int $hours): int
    {
        $this->info("Theme Event Metrics: {$eventType}");
        $this->info("Time Range: Last {$hours} hours");
        $this->newLine();

        $metrics = ThemeTelescopeMetrics::getEventMetrics($eventType, $hours);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Events', $metrics['total_events']],
                ['Error Count', $metrics['error_count']],
                ['Error Rate', number_format($metrics['error_rate'], 2).'%'],
                ['Latency Samples', $metrics['sample_count']],
                ['P50 Latency (ms)', number_format($metrics['latencies']['p50'], 2)],
                ['P95 Latency (ms)', number_format($metrics['latencies']['p95'], 2)],
                ['P99 Latency (ms)', number_format($metrics['latencies']['p99'], 2)],
                ['Max Latency (ms)', number_format($metrics['latencies']['max'], 2)],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Display aggregated metrics across all event types.
     */
    private function displayAggregatedMetrics(int $hours): int
    {
        $this->info('Aggregated Theme Event Metrics');
        $this->info("Time Range: Last {$hours} hours");
        $this->newLine();

        $metrics = ThemeTelescopeMetrics::getAggregatedMetrics($hours);

        $this->info('Overall Metrics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Events', $metrics['total_events']],
                ['Total Errors', $metrics['total_errors']],
                ['Overall Error Rate', number_format($metrics['overall_error_rate'], 2).'%'],
                ['P50 Latency (ms)', number_format($metrics['overall_latencies']['p50'], 2)],
                ['P95 Latency (ms)', number_format($metrics['overall_latencies']['p95'], 2)],
                ['P99 Latency (ms)', number_format($metrics['overall_latencies']['p99'], 2)],
                ['Max Latency (ms)', number_format($metrics['overall_latencies']['max'], 2)],
            ]
        );

        $this->newLine();
        $this->info('Metrics by Event Type:');

        foreach ($metrics['by_event_type'] as $eventType => $eventMetrics) {
            $this->line("  {$eventType}:");
            $this->line("    Events: {$eventMetrics['total_events']}");
            $this->line("    Errors: {$eventMetrics['error_count']} ({$eventMetrics['error_rate']}%)");
            $this->line('    P95 Latency: '.number_format($eventMetrics['latencies']['p95'], 2).' ms');
        }

        return Command::SUCCESS;
    }

    /**
     * Display performance metrics from ThemePerformanceTracker.
     */
    private function displayPerformanceMetrics(): int
    {
        $this->info('Theme Performance Metrics (from ThemePerformanceTracker)');
        $this->newLine();

        $operations = ['theme_save', 'theme_change', 'theme_preview'];

        foreach ($operations as $operation) {
            $metrics = ThemeTelescopeMetrics::getPerformanceMetrics($operation);

            $this->line("Operation: {$operation}");
            $this->table(
                ['Metric', 'P50', 'P95', 'P99', 'Max', 'Count'],
                [
                    [
                        'DOM Update Time (ms)',
                        number_format($metrics['dom_update_time']['p50'] ?? 0, 2),
                        number_format($metrics['dom_update_time']['p95'] ?? 0, 2),
                        number_format($metrics['dom_update_time']['p99'] ?? 0, 2),
                        number_format($metrics['dom_update_time']['max'] ?? 0, 2),
                        $metrics['dom_update_time']['count'] ?? 0,
                    ],
                    [
                        'Database Query Time (ms)',
                        number_format($metrics['database_query_time']['p50'] ?? 0, 2),
                        number_format($metrics['database_query_time']['p95'] ?? 0, 2),
                        number_format($metrics['database_query_time']['p99'] ?? 0, 2),
                        number_format($metrics['database_query_time']['max'] ?? 0, 2),
                        $metrics['database_query_time']['count'] ?? 0,
                    ],
                    [
                        'Total Time (ms)',
                        number_format($metrics['total_time']['p50'] ?? 0, 2),
                        number_format($metrics['total_time']['p95'] ?? 0, 2),
                        number_format($metrics['total_time']['p99'] ?? 0, 2),
                        number_format($metrics['total_time']['max'] ?? 0, 2),
                        $metrics['total_time']['count'] ?? 0,
                    ],
                ]
            );
            $this->newLine();
        }

        return Command::SUCCESS;
    }
}
