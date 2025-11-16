<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\artisan;

beforeEach(function (): void {
    Storage::fake('local');
});

it('fails when no dependency review reports are present', function (): void {
    // No files written to storage
    artisan('platform:dependency-review-performance-report')
        ->expectsOutputToContain('No dependency review reports are present in storage')
        ->assertExitCode(1);
});

it('fails when the report contains invalid JSON', function (): void {
    $reportPath = 'base-platform/dependency-reports/2025-11-dependency-review.json';
    Storage::disk('local')->put($reportPath, '{invalid json');

    artisan('platform:dependency-review-performance-report', [
        '--report' => $reportPath,
    ])->expectsOutputToContain('contains invalid JSON')
        ->assertExitCode(1);
});

it('fails when report file does not exist', function (): void {
    artisan('platform:dependency-review-performance-report', [
        '--report' => 'non-existent/report.json',
    ])->expectsOutputToContain('could not be found')
        ->assertExitCode(1);
});

it('fails when report does not decode to an array', function (): void {
    $reportPath = 'base-platform/dependency-reports/2025-11-dependency-review.json';
    Storage::disk('local')->put($reportPath, '"not an array"');

    artisan('platform:dependency-review-performance-report', [
        '--report' => $reportPath,
    ])->expectsOutputToContain('must decode to an array')
        ->assertExitCode(1);
});

it('uses first available report when no report specified', function (): void {
    Storage::disk('local')->put('base-platform/dependency-reports/2025-11-dependency-review.json', json_encode([
        'generated_at' => '2025-11-09T09:30:00+00:00',
        'runtime_seconds' => 1.5,
    ], JSON_THROW_ON_ERROR));

    artisan('platform:dependency-review-performance-report')
        ->assertExitCode(0);
});
