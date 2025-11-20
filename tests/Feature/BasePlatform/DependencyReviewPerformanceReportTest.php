<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\artisan;

beforeEach(function (): void {
    Storage::fake('local');
    Date::setTestNow('2025-11-09 10:00:00');
});

afterEach(function (): void {
    Date::setTestNow();
});

it('summarises dependency review runtime metrics and appends performance log', function (): void {
    // Arrange: seed a realistic dependency review report in the expected directory
    $reportPath = 'base-platform/dependency-reports/2025-11-dependency-review.json';

    $report = [
        'generated_at' => Date::now()->toIso8601String(),
        'status' => 'pass',
        'runtime_seconds' => 1.234,
        'severity_counts' => [
            'critical' => 0,
            'high' => 0,
            'medium' => 1,
            'low' => 2,
        ],
        'overdue_reviews' => [],
        'dependencies' => [],
        'issue_template' => '.github/ISSUE_TEMPLATE/dependency-review.md',
        'composer_command' => 'composer audit --format=json',
        'error' => null,
    ];

    Storage::disk('local')->put($reportPath, json_encode($report, JSON_THROW_ON_ERROR));

    // Act: run the performance report command against the seeded report
    artisan('platform:dependency-review-performance-report', [
        '--report' => $reportPath,
    ])->expectsOutputToContain('Dependency review performance entry recorded')
        ->assertExitCode(0);

    // Assert: performance log appended on the local disk
    $logPath = 'base-platform/dependency-performance.log';
    expect(Storage::disk('local')->exists($logPath))->toBeTrue();

    $entries = array_filter(
        explode("\n", (string) Storage::disk('local')->get($logPath)),
        static fn (string $line): bool => mb_trim($line) !== ''
    );

    /** @phpstan-var array<int, non-empty-string> $entries */
    expect($entries)->not->toBeEmpty();
    $lastEntry = end($entries);
    expect($lastEntry)->not->toBeFalse();
    $last = json_decode($lastEntry, true, 512, JSON_THROW_ON_ERROR);

    expect($last)->toMatchArray([
        'report_path' => $reportPath,
        'status' => 'pass',
        'overdue_count' => 0,
    ]);
});
