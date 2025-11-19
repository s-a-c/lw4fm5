<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

final class BasePlatformMetrics
{
    /**
     * @param  array<string, mixed>  $labels
     */
    public static function record(string $metric, array $labels = [], float|int $value = 1): void
    {
        $payload = [
            'metric' => self::formatMetric($metric),
            'labels' => Arr::map($labels, static function (mixed $label): string {
                if (is_string($label)) {
                    return $label;
                }
                if (is_scalar($label)) {
                    return (string) $label;
                }

                return '';
            }),
            'value' => $value,
            'timestamp' => now()->toIso8601String(),
        ];

        try {
            $channel = config('base-platform.observability.log_channel');
            $channelName = is_string($channel) ? $channel : null;
            Log::channel($channelName)
                ->info('base-platform-metric', $payload);
        } catch (Throwable) {
            // Silently fail if logging is unavailable (e.g., Monolog autoload issues)
            // This prevents CI failures when logging infrastructure has issues
        }
    }

    public static function recordBootstrapDuration(string $profile, float $minutes): void
    {
        self::record('bootstrap_duration_minutes', [
            'profile' => $profile,
        ], $minutes);
    }

    public static function recordValidationOutcome(string $profile, string $status): void
    {
        self::record('validation_outcome', [
            'profile' => $profile,
            'status' => $status,
        ]);
    }

    /**
     * @param  array<string, string>  $labels
     */
    public static function recordHealthCheck(string $check, bool $passed, array $labels = []): void
    {
        self::record('health_check', array_merge($labels, [
            'check' => $check,
            'status' => $passed ? 'pass' : 'fail',
        ]));
    }

    private static function formatMetric(string $metric): string
    {
        $prefixRaw = config('base-platform.observability.metrics_prefix');
        $prefixStr = is_string($prefixRaw) ? $prefixRaw : '';
        $prefix = Str::of($prefixStr)->trim('_');

        $normalized = Str::of($metric)
            ->replace('-', '_')
            ->trim('_');

        if ($prefix->isEmpty()) {
            return (string) $normalized;
        }

        $prefixWithUnderscore = (string) $prefix->append('_');

        return $prefixWithUnderscore.$normalized;
    }
}
