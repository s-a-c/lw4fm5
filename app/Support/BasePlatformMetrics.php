<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class BasePlatformMetrics
{
    public static function record(string $metric, array $labels = [], float|int $value = 1): void
    {
        $payload = [
            'metric' => self::formatMetric($metric),
            'labels' => Arr::map($labels, static fn ($label) => (string) $label),
            'value' => $value,
            'timestamp' => now()->toIso8601String(),
        ];

        Log::channel(config('base-platform.observability.log_channel'))
            ->info('base-platform-metric', $payload);
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

    private static function formatMetric(string $metric): string
    {
        $prefix = Str::of((string) config('base-platform.observability.metrics_prefix'))
            ->trim('_');

        $normalized = Str::of($metric)
            ->replace('-', '_')
            ->trim('_');

        if ($prefix->isEmpty()) {
            return (string) $normalized;
        }

        return (string) $prefix->append('_')->append($normalized);
    }
}
