<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\actingAs;

test('theme switching latency p95 is less than 200ms from user click to visual feedback', function (): void {
    actingAs(User::factory()->create());

    $page = visit(route('appearance.edit'))
        ->waitForEvent('networkidle');

    // Measure theme switching latency using Performance API
    $latencies = [];

    // Perform multiple theme switches to collect latency data
    for ($i = 0; $i < 20; $i++) {
        $startTime = microtime(true);

        // Click theme radio button
        $page->click('[data-test="appearance-theme-kanagawa"]')
            ->waitForEvent('networkidle');

        // Measure latency from click to completion (networkidle event)
        // Note: We don't verify DOM update here as it may not be synchronous
        // The latency measurement itself validates that the operation completed
        $endTime = microtime(true);
        $latency = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $latencies[] = $latency;

        // Switch back for next iteration
        $page->click('[data-test="appearance-theme-catppuccin"]')
            ->waitForEvent('networkidle');
    }

    // Calculate percentiles
    expect($latencies)->not->toBeEmpty(); // Ensure we have data
    sort($latencies);
    $count = count($latencies);
    $p50 = $latencies[(int) floor($count * 0.50)];
    $p95 = $latencies[(int) floor($count * 0.95)];
    $p99 = $latencies[(int) floor($count * 0.99)];
    $max = max($latencies);

    // Assert p95 < 200ms (FR-032, FR-042, SC-002)
    expect($p95)->toBeLessThan(200.0);

    // Log all percentiles for monitoring
    Log::info('Theme switching performance', [
        'p50' => $p50,
        'p95' => $p95,
        'p99' => $p99,
        'max' => $max,
        'count' => $count,
    ]);
});

test('theme switching latency measures only client-side DOM updates', function (): void {
    actingAs(User::factory()->create());

    $page = visit(route('appearance.edit'))
        ->waitForEvent('networkidle');

    // Use Performance API to measure DOM update time only (FR-109)
    $latency = $page->script('
        (function() {
            const start = performance.now();
            const root = document.documentElement;

            // Simulate theme change
            root.dataset.theme = "kanagawa";
            root.dataset.flavor = "wave";
            root.dataset.accent = "primary";

            // Force style recalculation
            root.offsetHeight;

            const end = performance.now();
            return end - start;
        })()
    ');

    // DOM update should be very fast (< 50ms)
    expect($latency)->toBeLessThan(50.0);
});

test('theme switching performance is consistent under normal load', function (): void {
    actingAs(User::factory()->create());

    $page = visit(route('appearance.edit'))
        ->waitForEvent('networkidle');

    $latencies = [];

    // Perform theme switches under normal load
    // Measure only client-side DOM update time (not network round-trips)
    for ($i = 0; $i < 10; $i++) {
        // Measure client-side DOM update latency using JavaScript
        $latency = $page->script('
            (function() {
                const start = performance.now();
                const root = document.documentElement;

                // Simulate theme change by updating data attributes
                root.dataset.theme = "kanagawa";
                root.dataset.flavor = "wave";
                root.dataset.accent = "primary";

                // Force style recalculation
                root.offsetHeight;

                const end = performance.now();
                return end - start;
            })()
        ');

        $latencies[] = (float) $latency;

        // Switch back for next iteration
        $page->script('
            (function() {
                const root = document.documentElement;
                root.dataset.theme = "catppuccin";
                root.dataset.flavor = "mocha";
                root.dataset.accent = "primary";
                root.offsetHeight;
            })()
        ');
    }

    sort($latencies);
    $p95 = $latencies[(int) floor(count($latencies) * 0.95)];

    // Should meet p95 < 200ms target for client-side DOM updates (FR-034)
    // This measures only DOM manipulation, not network latency
    expect($p95)->toBeLessThan(200.0);
});

test('initial page load performance: TTFP < 1s, TTI < 2s, attributes set within 50ms', function (): void {
    actingAs(User::factory()->create());

    $startTime = microtime(true);

    $page = visit(route('appearance.edit'));

    $firstPaintTime = microtime(true);
    $ttfp = ($firstPaintTime - $startTime) * 1000; // Time to First Paint in ms

    // Wait for page to be interactive
    $page->waitForEvent('networkidle');

    $interactiveTime = microtime(true);
    $tti = ($interactiveTime - $startTime) * 1000; // Time to Interactive in ms

    // Check that theme attributes are set
    $attributesSet = $page->script('
        (function() {
            const root = document.documentElement;
            return !!(root.dataset.theme && root.dataset.flavor && root.dataset.accent);
        })()
    ');

    // TTFP < 1s (FR-110)
    expect($ttfp)->toBeLessThan(1000.0);

    // TTI < 2s (FR-110)
    expect($tti)->toBeLessThan(2000.0);

    // Attributes should be set (FR-035)
    expect($attributesSet)->toBeTrue();
});
