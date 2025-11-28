<?php

declare(strict_types=1);

namespace App\Services\Theme;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Telescope\Telescope;

/**
 * Performance tracking for theme operations (T027e, FR-101).
 *
 * Tracks p50, p95, p99, max percentiles for:
 * - DOM update time (client-side, measured via Performance API)
 * - Database query time (server-side)
 * - Total time (end-to-end)
 */
final class ThemePerformanceTracker
{
    private const string CACHE_PREFIX = 'theme:performance:';

    private const int METRICS_RETENTION_HOURS = 24;

    private const int MAX_SAMPLES = 1000; // Limit samples to prevent memory issues

    /**
     * Record a performance metric (T027e, FR-101).
     *
     * @param  string  $operation  Operation name (e.g., 'theme_change', 'theme_save')
     * @param  float  $domUpdateTime  DOM update time in milliseconds
     * @param  float  $databaseQueryTime  Database query time in milliseconds
     * @param  float  $totalTime  Total time in milliseconds
     */
    public static function record(
        string $operation,
        float $domUpdateTime,
        float $databaseQueryTime,
        float $totalTime,
    ): void {
        $timestamp = now()->toIso8601String();

        // Store individual sample
        $sampleKey = self::CACHE_PREFIX.'sample:'.$operation.':'.uniqid('', true);
        $sample = [
            'timestamp' => $timestamp,
            'dom_update_time' => $domUpdateTime,
            'database_query_time' => $databaseQueryTime,
            'total_time' => $totalTime,
        ];

        Cache::put($sampleKey, $sample, now()->addHours(self::METRICS_RETENTION_HOURS));

        // Update aggregated metrics
        self::updateAggregatedMetrics($operation, $domUpdateTime, $databaseQueryTime, $totalTime);

        // Log performance marker (T027e, FR-101)
        Log::debug('Theme performance marker', [
            'event_type' => 'performance_marker',
            'operation' => $operation,
            'dom_update_time_ms' => $domUpdateTime,
            'database_query_time_ms' => $databaseQueryTime,
            'total_time_ms' => $totalTime,
            'timestamp' => $timestamp,
        ]);

        // Tag Telescope entry for filtering
        if (class_exists(Telescope::class)) {
            Telescope::tag(fn (): array => ['theme:performance', 'theme:event']);
        }
    }

    /**
     * Get performance percentiles for an operation (T027e, FR-101).
     *
     * @param  string  $operation  Operation name
     * @return array<string, array<string, float>> Percentiles for each metric type
     */
    public static function getPercentiles(string $operation): array
    {
        $metricsKey = self::CACHE_PREFIX.'metrics:'.$operation;
        $metrics = Cache::get($metricsKey, [
            'dom_update_time' => [],
            'database_query_time' => [],
            'total_time' => [],
        ]);

        return [
            'dom_update_time' => self::calculatePercentiles($metrics['dom_update_time'] ?? []),
            'database_query_time' => self::calculatePercentiles($metrics['database_query_time'] ?? []),
            'total_time' => self::calculatePercentiles($metrics['total_time'] ?? []),
        ];
    }

    /**
     * Update aggregated metrics with new sample (T027e, FR-101).
     */
    private static function updateAggregatedMetrics(
        string $operation,
        float $domUpdateTime,
        float $databaseQueryTime,
        float $totalTime,
    ): void {
        $metricsKey = self::CACHE_PREFIX.'metrics:'.$operation;
        $metrics = Cache::get($metricsKey, [
            'dom_update_time' => [],
            'database_query_time' => [],
            'total_time' => [],
        ]);

        // Add new samples
        $metrics['dom_update_time'][] = $domUpdateTime;
        $metrics['database_query_time'][] = $databaseQueryTime;
        $metrics['total_time'][] = $totalTime;

        // Limit samples to prevent memory issues
        if (count($metrics['dom_update_time']) > self::MAX_SAMPLES) {
            $metrics['dom_update_time'] = array_slice($metrics['dom_update_time'], -self::MAX_SAMPLES);
            $metrics['database_query_time'] = array_slice($metrics['database_query_time'], -self::MAX_SAMPLES);
            $metrics['total_time'] = array_slice($metrics['total_time'], -self::MAX_SAMPLES);
        }

        // Store updated metrics
        Cache::put($metricsKey, $metrics, now()->addHours(self::METRICS_RETENTION_HOURS));
    }

    /**
     * Calculate percentiles (p50, p95, p99, max) from array of values (T027e, FR-101).
     *
     * @param  array<float>  $values  Array of metric values
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
                'count' => 0,
            ];
        }

        sort($values);
        $count = count($values);

        return [
            'p50' => self::percentile($values, 50, $count),
            'p95' => self::percentile($values, 95, $count),
            'p99' => self::percentile($values, 99, $count),
            'max' => max($values),
            'count' => $count,
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
