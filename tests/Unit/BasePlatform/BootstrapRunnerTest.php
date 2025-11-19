<?php

declare(strict_types=1);

use App\Services\BasePlatform\BootstrapRecovery;
use App\Services\BasePlatform\BootstrapRun;
use App\Services\BasePlatform\BootstrapRunner;
use App\Services\BasePlatform\BootstrapRunnerException;
use App\Services\BasePlatform\UnsupportedProfileException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;

it('runs the bootstrap script successfully and returns a BootstrapRun', function (): void {
    Config::set('base-platform.profiles.supported', ['native']);

    $command = base_path('scripts/platform/bootstrap.sh');

    Process::fake([
        $command => Process::result(output: 'All good', errorOutput: '', exitCode: 0),
    ]);

    $runner = new BootstrapRunner(new BootstrapRecovery());

    $run = $runner->run('native', false);

    expect($run->profile)->toBe('native')
        ->and($run->isSuccessful())->toBeTrue()
        ->and($run->durationMinutes)->toBeFloat()
        ->and($run->notes['output'])->toBe('All good');
});

it('throws UnsupportedProfileException for an unsupported profile', function (): void {
    Config::set('base-platform.profiles.supported', ['container']);

    $runner = new BootstrapRunner(new BootstrapRecovery());

    expect(fn (): BootstrapRun => $runner->run('native', false))->toThrow(UnsupportedProfileException::class);
});

it('throws BootstrapRunnerException when the process fails', function (): void {
    Config::set('base-platform.profiles.supported', ['native']);

    $command = base_path('scripts/platform/bootstrap.sh');

    Process::fake([
        $command => Process::result(output: '', errorOutput: 'Some error', exitCode: 1),
    ]);

    $runner = new BootstrapRunner(new BootstrapRecovery());

    expect(fn (): BootstrapRun => $runner->run('native', true))->toThrow(BootstrapRunnerException::class);
});
