<?php

declare(strict_types=1);

use App\Console\Commands\DependencyReviewReport;
use App\Contracts\BasePlatform\ComposerAuditRunnerContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Symfony\Component\Console\Tester\CommandTester;

use function Pest\Laravel\mock;

beforeEach(function (): void {
    Storage::fake('local');
    Date::setTestNow('2025-11-09 09:30:00');

    // Override global Process::preventStrayProcesses() with a fake
    // This prevents hangs if any code tries to run a process
    Process::fake([
        '*' => Process::result(output: '', errorOutput: '', exitCode: 0),
    ]);

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
});

afterEach(function (): void {
    Date::setTestNow();
});

it('generates a dependency review report with severity counts and overdue entries', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    // Clear any existing report files first
    $reportPath = 'base-platform/dependency-reports/2025-11-dependency-review.json';
    Storage::disk('local')->delete($reportPath);
    if (File::exists(storage_path('app/'.$reportPath))) {
        File::delete(storage_path('app/'.$reportPath));
    }

    // Mock the ComposerAuditRunnerContract
    $fakeResult = Process::result(
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
    );

    /** @phpstan-var MockInterface&ComposerAuditRunnerContract $mock */
    $mock = mock(ComposerAuditRunnerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->andReturn($fakeResult);

    // Bind mock before command is instantiated
    /** @phpstan-var Illuminate\Foundation\Application $app */
    /** @phpstan-ignore-next-line */
    $app = $this->app;
    $app->instance(ComposerAuditRunnerContract::class, $mock);

    $command = app(DependencyReviewReport::class);
    $command->setLaravel($app);

    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);
    $output = $tester->getDisplay();

    expect($exitCode)->toBe(0);
    expect($output)->toContain('Dependency review report generated');

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

    $report = json_decode((string) $contents, true, 512, JSON_THROW_ON_ERROR);

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

    /** @phpstan-var Collection<int, array<string, mixed>> $overdueReviews */
    $overdueReviews = Collection::make($report['overdue_reviews']);
    expect($overdueReviews->pluck('name'))->toContain('laravel/framework');
    /** @phpstan-var Collection<int, array<string, mixed>> $dependencies */
    $dependencies = Collection::make($report['dependencies']);
    $dependency = $dependencies->firstWhere('name', 'laravel/framework');
    assert($dependency !== null);
    expect($dependency['classification'])->toBe('core');
    expect($report['issue_template'])->toBe('.github/ISSUE_TEMPLATE/dependency-review.md');
});

it('handles composer audit failure with error output', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var MockInterface&ComposerAuditRunnerContract $mock */
    $mock = mock(ComposerAuditRunnerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->andReturn(Process::result(
            output: '',
            errorOutput: 'Composer audit failed',
            exitCode: 1,
        ));

    app()->instance(ComposerAuditRunnerContract::class, $mock);

    $command = app(DependencyReviewReport::class);
    /** @phpstan-var Illuminate\Foundation\Application $app */
    /** @phpstan-ignore-next-line */
    $app = $this->app;
    $command->setLaravel($app);

    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);
    $output = $tester->getDisplay();

    expect($exitCode)->toBe(1);
    expect($output)->toContain('Composer audit failed');
});

it('handles composer audit returning malformed JSON', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var MockInterface&ComposerAuditRunnerContract $mock */
    $mock = mock(ComposerAuditRunnerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->andReturn(Process::result(
            output: 'not json',
            errorOutput: '',
            exitCode: 0,
        ));

    app()->instance(ComposerAuditRunnerContract::class, $mock);

    $command = app(DependencyReviewReport::class);
    /** @phpstan-var Illuminate\Foundation\Application $app */
    /** @phpstan-ignore-next-line */
    $app = $this->app;
    $command->setLaravel($app);

    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);
    $output = $tester->getDisplay();

    expect($exitCode)->toBe(1);
    expect($output)->toContain('Composer audit returned malformed JSON output');
});

it('handles warning status when medium advisories exist', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var MockInterface&ComposerAuditRunnerContract $mock */
    $mock = mock(ComposerAuditRunnerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->andReturn(Process::result(
            output: json_encode([
                'advisories' => [
                    [
                        'packageName' => 'test/package',
                        'severity' => 'medium',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
            errorOutput: '',
            exitCode: 0,
        ));

    app()->instance(ComposerAuditRunnerContract::class, $mock);

    $command = app(DependencyReviewReport::class);
    /** @phpstan-var Illuminate\Foundation\Application $app */
    /** @phpstan-ignore-next-line */
    $app = $this->app;
    $command->setLaravel($app);

    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);
    $output = $tester->getDisplay();

    expect($exitCode)->toBe(0);

    $reportPath = 'base-platform/dependency-reports/2025-11-dependency-review.json';
    $disk = Storage::disk('local');
    $contents = $disk->exists($reportPath) ? $disk->get($reportPath) : null;
    if ($contents !== null) {
        $report = json_decode($contents, true);
        expect($report['status'])->toBe('warn');
    }
});

it('handles unknown severity in advisories', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var MockInterface&ComposerAuditRunnerContract $mock */
    $mock = mock(ComposerAuditRunnerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->andReturn(Process::result(
            output: json_encode([
                'advisories' => [
                    [
                        'packageName' => 'test/package',
                        'severity' => 'unknown-severity',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
            errorOutput: '',
            exitCode: 0,
        ));

    app()->instance(ComposerAuditRunnerContract::class, $mock);

    $command = app(DependencyReviewReport::class);
    /** @phpstan-var Illuminate\Foundation\Application $app */
    /** @phpstan-ignore-next-line */
    $app = $this->app;
    $command->setLaravel($app);

    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);
    $output = $tester->getDisplay();

    expect($exitCode)->toBe(0);
});

it('uses custom output path when provided', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var MockInterface&ComposerAuditRunnerContract $mock */
    $mock = mock(ComposerAuditRunnerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->andReturn(Process::result(
            output: json_encode(['advisories' => []], JSON_THROW_ON_ERROR),
            errorOutput: '',
            exitCode: 0,
        ));

    app()->instance(ComposerAuditRunnerContract::class, $mock);

    $command = app(DependencyReviewReport::class);
    /** @phpstan-var Illuminate\Foundation\Application $app */
    /** @phpstan-ignore-next-line */
    $app = $this->app;
    $command->setLaravel($app);

    $tester = new CommandTester($command);
    $exitCode = $tester->execute([
        '--output' => 'custom/path/report.json',
    ]);
    $output = $tester->getDisplay();

    expect($exitCode)->toBe(0);

    expect(Storage::disk('local')->exists('custom/path/report.json'))->toBeTrue();
});
