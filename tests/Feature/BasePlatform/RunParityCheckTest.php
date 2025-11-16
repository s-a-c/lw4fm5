<?php

declare(strict_types=1);

use App\Console\Commands\RunParityCheck;
use App\Contracts\BasePlatform\ParityCheckerContract;
use App\Models\EnvironmentProfile;
use App\Services\BasePlatform\ParityReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

it('handles empty profiles list', function (): void {
    Config::set('base-platform.profiles.supported', []);

    $mock = mock(ParityCheckerContract::class);
    $mock->shouldNotReceive('run');

    app()->instance(ParityCheckerContract::class, $mock);

    $result = Artisan::call(RunParityCheck::class);

    expect($result)->toBe(1);
    expect(Artisan::output())->toContain('No supported profiles registered');
});

it('handles warning status in parity results', function (): void {
    Config::set('base-platform.profiles.supported', ['native']);

    $profile = EnvironmentProfile::query()->create([
        'name' => 'native',
        'runtime_versions' => ['php' => '8.4'],
        'prerequisites' => [],
        'smoke_check_script' => null,
        'status' => 'supported',
    ]);

    $report = new ParityReport(
        profile: 'native',
        status: ParityReport::STATUS_WARNING,
        issues: ['Warning: Some configuration drift detected'],
    );

    $mock = mock(ParityCheckerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->with(['native'])
        ->andReturn([$report]);

    app()->instance(ParityCheckerContract::class, $mock);

    $result = Artisan::call(RunParityCheck::class);

    expect($result)->toBe(0);
    $output = Artisan::output();
    expect($output)->toContain('Parity check finished with warnings for native');
    expect($output)->toContain('Warning: Some configuration drift detected');
});

it('handles non-string issues in parity results', function (): void {
    Config::set('base-platform.profiles.supported', ['native']);

    $profile = EnvironmentProfile::query()->create([
        'name' => 'native',
        'runtime_versions' => ['php' => '8.4'],
        'prerequisites' => [],
        'smoke_check_script' => null,
        'status' => 'supported',
    ]);

    $report = new ParityReport(
        profile: 'native',
        status: ParityReport::STATUS_FAIL,
        issues: [
            'String issue',
            123, // Non-string issue
            ['array' => 'issue'], // Non-string issue
        ],
    );

    $mock = mock(ParityCheckerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->with(['native'])
        ->andReturn([$report]);

    app()->instance(ParityCheckerContract::class, $mock);

    $result = Artisan::call(RunParityCheck::class);

    expect($result)->toBe(0);
    $output = Artisan::output();
    expect($output)->toContain('Parity check failed for native');
    expect($output)->toContain('String issue');
    expect($output)->not->toContain('123');
    expect($output)->not->toContain('array');
});

it('filters profiles by option', function (): void {
    EnvironmentProfile::query()->create([
        'name' => 'native',
        'runtime_versions' => ['php' => '8.4'],
        'prerequisites' => [],
        'smoke_check_script' => null,
        'status' => 'supported',
    ]);
    EnvironmentProfile::query()->create([
        'name' => 'container',
        'runtime_versions' => ['php' => '8.4'],
        'prerequisites' => [],
        'smoke_check_script' => null,
        'status' => 'supported',
    ]);

    $report = new ParityReport(
        profile: 'native',
        status: ParityReport::STATUS_PASS,
        issues: [],
    );

    $mock = mock(ParityCheckerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->with(['native'])
        ->andReturn([$report]);

    app()->instance(ParityCheckerContract::class, $mock);

    $result = Artisan::call(RunParityCheck::class, [
        '--profile' => 'native',
    ]);

    expect($result)->toBe(0);
});

it('handles non-array supported profiles config', function (): void {
    Config::set('base-platform.profiles.supported', 'not-an-array');

    $mock = mock(ParityCheckerContract::class);
    $mock->shouldNotReceive('run');

    app()->instance(ParityCheckerContract::class, $mock);

    $result = Artisan::call(RunParityCheck::class);

    expect($result)->toBe(1);
    expect(Artisan::output())->toContain('No supported profiles registered');
});

it('handles fail status in parity results', function (): void {
    Config::set('base-platform.profiles.supported', ['native']);

    $profile = EnvironmentProfile::query()->create([
        'name' => 'native',
        'runtime_versions' => ['php' => '8.4'],
        'prerequisites' => [],
        'smoke_check_script' => null,
        'status' => 'supported',
    ]);

    $report = new ParityReport(
        profile: 'native',
        status: ParityReport::STATUS_FAIL, // Use valid status that matches default case in renderStatusMessage
        issues: ['Critical issue'],
    );

    $mock = mock(ParityCheckerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->with(['native'])
        ->andReturn([$report]);

    app()->instance(ParityCheckerContract::class, $mock);

    $result = Artisan::call(RunParityCheck::class);

    expect($result)->toBe(0);
    expect(Artisan::output())->toContain('Parity check failed for native');
});
