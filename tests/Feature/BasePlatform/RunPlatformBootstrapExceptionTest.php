<?php

declare(strict_types=1);

use App\Console\Commands\RunPlatformBootstrap;
use App\Contracts\BasePlatform\BootstrapRunnerContract;
use App\Data\BootstrapRecoveryGuidanceData;
use App\Services\BasePlatform\BootstrapRunnerException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;

it('handles BootstrapRunnerException with guidance', function (): void {
    Config::set('base-platform.profiles.supported', ['native']);

    $guidance = new BootstrapRecoveryGuidanceData(
        title: 'Bootstrap failed',
        documentation: 'https://example.com/docs',
        nextSteps: [
            'Step 1: Check logs',
            'Step 2: Verify credentials',
        ],
    );

    $exception = new BootstrapRunnerException(
        message: 'Bootstrap failed',
        output: 'Command output here',
        guidance: $guidance,
    );

    /** @phpstan-var MockInterface&BootstrapRunnerContract $mock */
    $mock = mock(BootstrapRunnerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->with('native', false)
        ->andThrow($exception);

    app()->instance(BootstrapRunnerContract::class, $mock);

    $result = Artisan::call(RunPlatformBootstrap::class, [
        '--profile' => 'native',
    ]);

    expect($result)->toBe(1);
    $output = Artisan::output();
    expect($output)->toContain('Bootstrap failed');
    expect($output)->toContain('Documentation: https://example.com/docs');
    expect($output)->toContain('1. Step 1: Check logs');
    expect($output)->toContain('2. Step 2: Verify credentials');
});
