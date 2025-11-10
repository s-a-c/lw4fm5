<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\PendingCommand;

use function Pest\Laravel\artisan;

beforeEach(function (): void {
    Storage::fake('local');
    Carbon::setTestNow('2025-11-09 09:30:00');

    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'laravel/framework',
            'version' => '12.0.0',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Application runtime and foundation',
            'lastReviewedAt' => '2025-07-01',
            'reviewCadence' => 'monthly',
            'riskLevel' => 'high',
            'notes' => 'Pinned to LTS release; breaking upgrades require ADR',
        ],
        [
            'name' => 'spatie/laravel-ignition',
            'version' => '2.1.4',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Error page handling and exception reporting',
            'lastReviewedAt' => '2025-10-31',
            'reviewCadence' => 'monthly',
            'riskLevel' => 'medium',
            'notes' => 'Track security advisories via composer audit',
        ],
    ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

    Process::fake([
        'composer audit --format=json' => Process::result(
            output: json_encode([
                'advisories' => [
                    [
                        'packageName' => 'laravel/framework',
                        'advisoryId' => 'CVE-2025-0001',
                        'title' => 'Example critical advisory',
                        'cve' => 'CVE-2025-0001',
                        'severity' => 'critical',
                    ],
                    [
                        'packageName' => 'spatie/laravel-ignition',
                        'advisoryId' => 'CVE-2025-0002',
                        'title' => 'Example medium advisory',
                        'cve' => 'CVE-2025-0002',
                        'severity' => 'medium',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
            errorOutput: '',
            exitCode: 0,
        ),
    ]);
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('generates a dependency review report with severity counts and overdue entries', function (): void {
    /** @var PendingCommand $command */
    $command = artisan('platform:dependency-review');

    $command
        ->expectsOutputToContain('Dependency review report generated')
        ->assertExitCode(0);

    $reportPath = 'base-platform/dependency-reports/2025-11-dependency-review.json';

    $disk = Storage::disk('local');
    $reportExists = $disk->exists($reportPath);

    if (! $reportExists) {
        $reportExists = File::exists(storage_path('app/'.$reportPath));
    }

    expect($reportExists)->toBeTrue();

    $contents = $disk->exists($reportPath)
        ? $disk->get($reportPath)
        : File::get(storage_path('app/'.$reportPath));

    $report = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

    expect($report)->toHaveKeys([
        'generated_at',
        'status',
        'runtime_seconds',
        'severity_counts',
        'overdue_reviews',
        'dependencies',
        'issue_template',
    ]);

    expect($report['severity_counts'])->toMatchArray([
        'critical' => 1,
        'high' => 0,
        'medium' => 1,
        'low' => 0,
    ]);

    expect(Collection::make($report['overdue_reviews'])->pluck('name'))->toContain('laravel/framework');
    expect(Collection::make($report['dependencies'])->firstWhere('name', 'laravel/framework')['classification'])->toBe('core');
    expect($report['issue_template'])->toBe('.github/ISSUE_TEMPLATE/dependency-review.md');
});
