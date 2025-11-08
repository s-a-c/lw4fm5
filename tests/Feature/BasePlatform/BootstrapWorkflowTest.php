<?php

declare(strict_types=1);

use App\Contracts\BasePlatform\BootstrapRunnerContract;
use App\Services\BasePlatform\BootstrapRun;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\artisan;
use function Pest\Laravel\mock;

it('delegates bootstrap execution to the runner for a supported profile', function (): void {
    Config::set('base-platform.profiles.supported', ['native', 'container']);

    $run = new BootstrapRun(
        profile: 'native',
        status: BootstrapRun::STATUS_SUCCESS,
        durationMinutes: 12.5,
        notes: ['queues' => 'ok']
    );

    mock(BootstrapRunnerContract::class)
        ->shouldReceive('run')
        ->once()
        ->with('native', false)
        ->andReturn($run);

    artisan('platform:bootstrap', ['--profile' => 'native'])
        ->expectsOutputToContain('Bootstrap complete for native')
        ->assertExitCode(0);
});

it('fails fast when requesting an unsupported profile', function (): void {
    Config::set('base-platform.profiles.supported', ['native']);

    artisan('platform:bootstrap', ['--profile' => 'container'])
        ->expectsOutputToContain('Unsupported profile [container]')
        ->assertExitCode(1);
});
