<?php

declare(strict_types=1);

use App\Support\BasePlatformMetrics;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

it('normalizes metric names and forwards payload to configured channel', function (): void {
    Config::set('base-platform.observability.log_channel', 'stack');
    Config::set('base-platform.observability.metrics_prefix', 'base_platform');

    Log::shouldReceive('channel')
        ->once()
        ->with('stack')
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->with('base-platform-metric', Mockery::on(function (array $payload): bool {
            /** @phpstan-var array{metric: string, labels: array<string, mixed>, value: int|float, timestamp: string} $payload */
            expect($payload['metric'])->toBe('base_platform_bootstrap_duration');
            expect($payload['labels'])->toBe(['profile' => 'native']);
            expect($payload['value'])->toBe(42);
            expect($payload)->toHaveKey('timestamp');

            return true;
        }));

    BasePlatformMetrics::record('bootstrap-duration', ['profile' => 'native'], 42);
});

it('records bootstrap duration helper metric', function (): void {
    Config::set('base-platform.observability.log_channel', 'stack');
    Config::set('base-platform.observability.metrics_prefix', 'base_platform');

    Log::shouldReceive('channel')
        ->once()
        ->with('stack')
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->with('base-platform-metric', Mockery::on(function (array $payload): bool {
            /** @phpstan-var array{metric: string, labels: array<string, mixed>, value: int|float} $payload */
            expect($payload['metric'])->toBe('base_platform_bootstrap_duration_minutes');
            expect($payload['labels'])->toBe(['profile' => 'container']);
            expect($payload['value'])->toBe(15.25);

            return true;
        }));

    BasePlatformMetrics::recordBootstrapDuration('container', 15.25);
});
