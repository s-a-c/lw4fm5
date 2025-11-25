<?php

declare(strict_types=1);

use App\Contracts\BasePlatform\BootstrapRunnerContract;
use App\Data\BootstrapRunData;
use App\Enums\BootstrapStatus;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;

use function Pest\Laravel\artisan;
use function Pest\Laravel\mock;

it('delegates bootstrap execution to the runner for a supported profile', function (): void {
    Config::set('base-platform.profiles.supported', ['native', 'container']);

    $run = new BootstrapRunData(
        profile: 'native',
        status: BootstrapStatus::Success,
        durationMinutes: 12.5,
        notes: ['queues' => 'ok']
    );

    /** @phpstan-var MockInterface&BootstrapRunnerContract $mock */
    $mock = mock(BootstrapRunnerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->with('native', false)
        ->andReturn($run);

    app()->instance(BootstrapRunnerContract::class, $mock);

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
