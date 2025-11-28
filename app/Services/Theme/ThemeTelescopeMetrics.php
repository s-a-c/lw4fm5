<?php

declare(strict_types=1);

namespace App\Services\Theme;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service to query and aggregate theme-related metrics from Telescope (T027d, FR-099).
 *
 * Provides methods to calculate:
 * - p50, p95, p99 latencies for theme events
 * - Event counts by type
 * - Error rates
 */
final class ThemeTelescopeMetrics
{
    /**
     * Get theme event metrics from Telescope (T027d, FR-099).
     *
     * @param  string  $eventType  Event type to filter (e.g., 'theme_changed', 'validation_corrected', 'preview_interaction')
     * @param  int  $hours  Number of hours to look back (default: 24)
     * @return array<string, mixed> Metrics including latencies, event counts, error rates
     */
    public static function getEventMetrics(string $eventType, int $hours = 24): array
    {
        $since = now()->subHours($hours);

        // Query Telescope log entries with theme event tags or event_type in context
        /** @var Collection<int, object{content: string, level: string}> $entries */
        $entries = DB::table('telescope_entries')
            ->where('type', 'log')
            ->where('created_at', '>=', $since)
            ->where(function (Builder $query) use ($eventType): void {
                $query->whereJsonContains('tags', "theme:{$eventType}")
                    ->orWhereJsonContains('content->context->event_type', $eventType);
            })->latest()
            ->get();

        // Extract performance metrics from entries
        $latencies = [];
        $errorCount = 0;
        $totalCount = $entries->count();

        foreach ($entries as $entry) {
            /** @var array{content: string, level: string, context?: array<string, mixed>} $content */
            $content = json_decode((string) $entry->content, true, 512, JSON_THROW_ON_ERROR);
            /** @var array<string, mixed> $context */
            $context = $content['context'] ?? [];

            // Extract latency if available (from performance metrics)
            if (isset($context['performance'])) {
                $performance = $context['performance'];
                if (isset($performance['total_time_ms'])) {
                    $latencies[] = (float) $performance['total_time_ms'];
                } elseif (isset($performance['dom_update_time'])) {
                    $latencies[] = (float) $performance['dom_update_time'];
                }
            }

            // Count errors (log level 'error' or 'warning' for validation_corrected)
            if ($content['level'] === 'error' || ($eventType === 'validation_corrected' && $content['level'] === 'warning')) {
                $errorCount++;
            }
        }

        // Calculate percentiles
        $percentiles = self::calculatePercentiles($latencies);

        return [
            'event_type' => $eventType,
            'time_range_hours' => $hours,
            'total_events' => $totalCount,
            'error_count' => $errorCount,
            'error_rate' => $totalCount > 0 ? ($errorCount / $totalCount) * 100 : 0.0,
            'latencies' => $percentiles,
            'sample_count' => count($latencies),
        ];
    }

    /**
     * Get aggregated theme metrics across all event types (T027d, FR-099).
     *
     * @param  int  $hours  Number of hours to look back (default: 24)
     * @return array<string, mixed> Aggregated metrics
     */
    public static function getAggregatedMetrics(int $hours = 24): array
    {
        $eventTypes = ['theme_changed', 'validation_corrected', 'preview_interaction'];

        $metrics = [];
        $allLatencies = [];
        $totalEvents = 0;
        $totalErrors = 0;

        foreach ($eventTypes as $eventType) {
            $eventMetrics = self::getEventMetrics($eventType, $hours);
            $metrics[$eventType] = $eventMetrics;
            $totalEvents += $eventMetrics['total_events'];
            $totalErrors += $eventMetrics['error_count'];

            // Collect latencies for overall percentiles
            if (isset($eventMetrics['latencies']['p50'])) {
                $allLatencies[] = $eventMetrics['latencies']['p50'];
                $allLatencies[] = $eventMetrics['latencies']['p95'];
                $allLatencies[] = $eventMetrics['latencies']['p99'];
            }
        }

        $overallPercentiles = self::calculatePercentiles($allLatencies);

        return [
            'time_range_hours' => $hours,
            'total_events' => $totalEvents,
            'total_errors' => $totalErrors,
            'overall_error_rate' => $totalEvents > 0 ? ($totalErrors / $totalEvents) * 100 : 0.0,
            'overall_latencies' => $overallPercentiles,
            'by_event_type' => $metrics,
        ];
    }

    /**
     * Get performance metrics from ThemePerformanceTracker and combine with Telescope data (T027d, FR-099).
     *
     * @param  string  $operation  Operation name (e.g., 'theme_save', 'theme_change')
     * @return array<string, mixed> Combined performance metrics
     */
    public static function getPerformanceMetrics(string $operation): array
    {
        // Get percentiles from ThemePerformanceTracker
        $trackerPercentiles = ThemePerformanceTracker::getPercentiles($operation);

        return [
            'operation' => $operation,
            'dom_update_time' => $trackerPercentiles['dom_update_time'] ?? [],
            'database_query_time' => $trackerPercentiles['database_query_time'] ?? [],
            'total_time' => $trackerPercentiles['total_time'] ?? [],
        ];
    }

    /**
     * Calculate percentiles (p50, p95, p99, max) from array of values (T027d, FR-099).
     *
     * @param  array<float>  $values  Array of latency values
     * @return array<string, float> Percentiles
     */
    private static function calculatePercentiles(array $values): array
    {
        if ($values === []) {
            return [
                'p50' => 0.0,
                'p95' => 0.0,
                'p99' => 0.0,
                'max' => 0.0,
            ];
        }

        sort($values);
        $count = count($values);

        return [
            'p50' => self::percentile($values, 50, $count),
            'p95' => self::percentile($values, 95, $count),
            'p99' => self::percentile($values, 99, $count),
            'max' => max($values),
        ];
    }

    /**
     * Calculate a specific percentile from sorted array.
     *
     * @param  array<float>  $sortedValues  Sorted array of values
     * @param  int  $percentile  Percentile to calculate (0-100)
     * @param  int  $count  Number of values
     */
    private static function percentile(array $sortedValues, int $percentile, int $count): float
    {
        if ($count === 0) {
            return 0.0;
        }

        $index = (int) ceil(($percentile / 100) * $count) - 1;
        $index = max(0, min($index, $count - 1));

        return $sortedValues[$index];
    }
}
