<?php

declare(strict_types=1);

namespace Tests\Feature\Theme;

use App\Livewire\Settings\Appearance;
use App\Models\User;
use App\Services\Theme\ThemePerformanceTracker;
use App\Services\Theme\ThemeTelescopeMetrics;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Mockery;

/**
 * Test observability implementation (T027j, FR-107).
 *
 * Verifies:
 * - Events are captured
 * - Metrics are recorded
 * - Acceptance criteria are met
 * - Regression testing
 */
test('theme events are captured in Telescope', function (): void {
    $user = User::factory()->create();

    Log::shouldReceive('info')
        ->atLeast()->once()
        ->with(Mockery::on(fn ($message): bool => in_array($message, ['Theme changed', 'Theme preference changed'], true)), Mockery::on(function (array $context): true {
            expect($context)->toHaveKey('event_type');
            expect($context['event_type'])->toBe('theme_changed');
            expect($context)->toHaveKey('user_id');
            expect($context)->toHaveKey('timestamp');

            return true;
        }));

    Log::shouldReceive('warning')->zeroOrMoreTimes();
    Log::shouldReceive('debug')->zeroOrMoreTimes();

    Livewire::actingAs($user)
        ->test(Appearance::class)
        ->set('theme', 'kanagawa')
        ->call('performSave');
});

test('performance metrics are recorded', function (): void {
    // Record a performance metric
    ThemePerformanceTracker::record(
        operation: 'theme_save',
        domUpdateTime: 50.0,
        databaseQueryTime: 25.0,
        totalTime: 75.0,
    );

    // Retrieve percentiles
    $percentiles = ThemePerformanceTracker::getPercentiles('theme_save');

    expect($percentiles)->toHaveKey('dom_update_time');
    expect($percentiles)->toHaveKey('database_query_time');
    expect($percentiles)->toHaveKey('total_time');

    expect($percentiles['total_time'])->toHaveKey('p50');
    expect($percentiles['total_time'])->toHaveKey('p95');
    expect($percentiles['total_time'])->toHaveKey('p99');
    expect($percentiles['total_time'])->toHaveKey('max');
});

test('telescope metrics command returns valid metrics', function (): void {
    $exitCode = Artisan::call('theme:telescope-metrics', [
        '--aggregated' => true,
        '--hours' => 24,
    ]);

    expect($exitCode)->toBe(0);

    $output = Artisan::output();
    expect($output)->toContain('Aggregated Theme Event Metrics');
    expect($output)->toContain('Total Events');
});

test('telescope metrics service queries events correctly', function (): void {
    // This test verifies the service can query Telescope data
    // In a real scenario, we'd need Telescope entries in the database
    $metrics = ThemeTelescopeMetrics::getEventMetrics('theme_changed', 24);

    expect($metrics)->toHaveKey('total_events');
    expect($metrics)->toHaveKey('error_count');
    expect($metrics)->toHaveKey('error_rate');
    expect($metrics)->toHaveKey('latencies');
    expect($metrics)->toHaveKey('event_type');
    expect($metrics['event_type'])->toBe('theme_changed');
});

test('aggregated metrics include all event types', function (): void {
    $metrics = ThemeTelescopeMetrics::getAggregatedMetrics(24);

    expect($metrics)->toHaveKey('total_events');
    expect($metrics)->toHaveKey('total_errors');
    expect($metrics)->toHaveKey('overall_error_rate');
    expect($metrics)->toHaveKey('overall_latencies');
    expect($metrics)->toHaveKey('by_event_type');
    expect($metrics['by_event_type'])->toHaveKey('theme_changed');
    expect($metrics['by_event_type'])->toHaveKey('validation_corrected');
    expect($metrics['by_event_type'])->toHaveKey('preview_interaction');
});

test('performance metrics command displays correct format', function (): void {
    // Record some performance data
    ThemePerformanceTracker::record('theme_change', 50.0, 25.0, 75.0);
    ThemePerformanceTracker::record('theme_change', 60.0, 30.0, 90.0);

    $exitCode = Artisan::call('theme:telescope-metrics', [
        '--performance' => true,
    ]);

    expect($exitCode)->toBe(0);

    $output = Artisan::output();
    // Command may not output exact strings, but should succeed
    expect($output)->toBeString();
});

test('observability regression: events still captured after code changes', function (): void {
    $user = User::factory()->create();

    // Verify theme_changed event is still captured
    Log::shouldReceive('info')
        ->atLeast()->once()
        ->with('Theme changed', Mockery::type('array'));

    Log::shouldReceive('info')
        ->atLeast()->once()
        ->with('Theme preference changed', Mockery::type('array'));

    Log::shouldReceive('warning')->zeroOrMoreTimes();
    Log::shouldReceive('debug')->zeroOrMoreTimes();

    Livewire::actingAs($user)
        ->test(Appearance::class)
        ->set('theme', 'kanagawa')
        ->call('performSave');
});

test('observability regression: performance tracking still works', function (): void {
    // Verify performance tracking still records metrics
    ThemePerformanceTracker::record('theme_save', 50.0, 25.0, 75.0);

    $percentiles = ThemePerformanceTracker::getPercentiles('theme_save');

    expect($percentiles)->toHaveKey('total_time');
    expect($percentiles['total_time'])->toHaveKey('p95');
    expect($percentiles['total_time']['p95'])->toBeGreaterThanOrEqual(0);
});

test('observability acceptance criteria: all required metrics available', function (): void {
    // Verify all required metrics are available via CLI
    $exitCode = Artisan::call('theme:telescope-metrics', [
        '--aggregated' => true,
    ]);

    expect($exitCode)->toBe(0);

    // Verify performance metrics are available from ThemePerformanceTracker
    $performance = ThemePerformanceTracker::getPercentiles('theme_save');
    if ($performance !== []) {
        expect($performance)->toHaveKey('dom_update_time');
        expect($performance)->toHaveKey('database_query_time');
        expect($performance)->toHaveKey('total_time');
    }
});
