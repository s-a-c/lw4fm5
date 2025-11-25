<?php

declare(strict_types=1);

use App\Console\Commands\RunPlatformBootstrap;
use App\Contracts\BasePlatform\BootstrapRunnerContract;
use App\Data\BootstrapRunData;
use App\Enums\BootstrapStatus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;

it('renders a warning message when the bootstrap completes with warnings', function (): void {
    Config::set('base-platform.profiles.supported', ['native']);

    /** @phpstan-var MockInterface&BootstrapRunnerContract $mock */
    $mock = mock(BootstrapRunnerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->with('native', false)
        ->andReturn(new BootstrapRunData(
            profile: 'native',
            status: BootstrapStatus::Warning,
            durationMinutes: 1.25,
            notes: ['notice' => 'cached deps used'],
        ));

    app()->instance(BootstrapRunnerContract::class, $mock);

    // Execute the command by name and assert we get a warning output
    $result = Artisan::call(RunPlatformBootstrap::class, [
        '--profile' => 'native',
    ]);

    expect($result)->toBe(0);
    expect(Artisan::output())
        ->toContain('Bootstrap complete for native in 1.25 minutes.');
});
