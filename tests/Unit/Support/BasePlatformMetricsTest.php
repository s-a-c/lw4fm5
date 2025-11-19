<?php

declare(strict_types=1);

use App\Support\BasePlatformMetrics;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery as m;

it('records a validation outcome with prefixed metric name', function (): void {
    Config::set('base-platform.observability.metrics_prefix', 'base_platform');
    Config::set('base-platform.observability.log_channel', 'stack');

    // Mock chained channel()->info call
    Log::shouldReceive('channel')
        ->with('stack')
        ->once()
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->with(
            'base-platform-metric',
            m::on(fn (array $payload): bool =>
                /** @phpstan-var array{metric?: string, labels?: array<string, mixed>, value?: int|float, timestamp?: string} $payload */
                ($payload['metric'] ?? null) === 'base_platform_validation_outcome'
                && ($payload['labels']['profile'] ?? null) === 'native'
                && ($payload['labels']['status'] ?? null) === 'pass'
                && is_numeric($payload['value'] ?? null)
                && is_string($payload['timestamp'] ?? null))
        );

    BasePlatformMetrics::recordValidationOutcome('native', 'pass');
});

it('records health check metrics for pass and fail', function (): void {
    Config::set('base-platform.observability.metrics_prefix', '');
    Config::set('base-platform.observability.log_channel', 'stack');

    // Mock twice for pass and fail
    Log::shouldReceive('channel')
        ->with('stack')
        ->twice()
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->with(
            'base-platform-metric',
            m::on(fn (array $payload): bool =>
                /** @phpstan-var array{metric?: string, labels?: array<string, mixed>} $payload */
                ($payload['metric'] ?? null) === 'health_check'
                && ($payload['labels']['check'] ?? null) === 'redis'
                && ($payload['labels']['status'] ?? null) === 'pass'
                && ($payload['labels']['region'] ?? null) === 'us')
        );

    Log::shouldReceive('info')
        ->once()
        ->with(
            'base-platform-metric',
            m::on(fn (array $payload): bool => ($payload['metric'] ?? null) === 'health_check'
                && ($payload['labels']['check'] ?? null) === 'redis'
                && ($payload['labels']['status'] ?? null) === 'fail'
                && ($payload['labels']['region'] ?? null) === 'us')
        );

    BasePlatformMetrics::recordHealthCheck('redis', true, ['region' => 'us']);
    BasePlatformMetrics::recordHealthCheck('redis', false, ['region' => 'us']);
});

it('handles non-string labels gracefully', function (): void {
    Config::set('base-platform.observability.log_channel', 'stack');

    Log::shouldReceive('channel')
        ->with('stack')
        ->once()
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->with(
            'base-platform-metric',
            m::on(function (array $payload): bool {
                /** @phpstan-var array{labels?: array<string, mixed>} $payload */
                $labels = $payload['labels'] ?? [];

                // String labels remain strings
                // Scalar labels (int, float, bool) are converted to strings
                // Non-scalar labels (arrays, objects) become empty strings
                return ($labels['string_label'] ?? null) === 'value'
                    && ($labels['int_label'] ?? null) === '123'  // Scalar converted to string
                    && ($labels['array_label'] ?? null) === '';    // Non-scalar becomes empty string
            })
        );

    BasePlatformMetrics::record('test_metric', [
        'string_label' => 'value',
        'int_label' => 123,
        'array_label' => ['nested' => 'value'],
    ]);
});

it('handles logging failures gracefully', function (): void {
    Config::set('base-platform.observability.log_channel', 'stack');

    // Simulate logging failure by making channel throw
    Log::shouldReceive('channel')
        ->with('stack')
        ->once()
        ->andThrow(new RuntimeException('Logging failed'));

    // Should not throw, should fail silently
    BasePlatformMetrics::record('test_metric', ['label' => 'value']);
    expect(true)->toBeTrue(); // If we get here, the exception was caught
});
