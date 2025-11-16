<?php

declare(strict_types=1);

use App\Models\EnvironmentProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

it('validates all supported profiles when --all is provided', function (): void {
    Config::set('base-platform.profiles.supported', ['native', 'container']);

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

    artisan('platform:validate-profiles', ['--all' => true])
        ->expectsOutputToContain('Validation complete for native')
        ->expectsOutputToContain('Validation complete for container')
        ->assertExitCode(0);
});

it('validates a specific supported profile when --profile is provided', function (): void {
    Config::set('base-platform.profiles.supported', ['native', 'container']);

    EnvironmentProfile::query()->create([
        'name' => 'native',
        'runtime_versions' => ['php' => '8.4'],
        'prerequisites' => [],
        'smoke_check_script' => null,
        'status' => 'supported',
    ]);

    artisan('platform:validate-profiles', ['--profile' => 'native'])
        ->expectsOutputToContain('Validation complete for native')
        ->assertExitCode(0);
});

it('fails with a helpful message when no profiles are found', function (): void {
    Config::set('base-platform.profiles.supported', []);

    artisan('platform:validate-profiles')
        ->expectsOutputToContain('No profiles found to validate')
        ->assertExitCode(1);
});

it('handles fail status in validation results', function (): void {
    Config::set('base-platform.profiles.supported', ['native']);

    // Create a profile that will fail validation
    EnvironmentProfile::query()->create([
        'name' => 'native',
        'runtime_versions' => ['php' => '8.4'],
        'prerequisites' => [],
        'smoke_check_script' => 'invalid-script-that-fails',
        'status' => 'supported',
    ]);

    // Mock the validator to return a fail result
    $validator = mock(App\Contracts\BasePlatform\EnvironmentProfileValidatorContract::class);
    $validator->shouldReceive('validate')
        ->once()
        ->andReturn([
            new App\Services\BasePlatform\ProfileValidationResult(
                profile: 'native',
                status: 'fail',
                issues: ['Validation failed'],
            ),
        ]);

    app()->instance(App\Contracts\BasePlatform\EnvironmentProfileValidatorContract::class, $validator);

    artisan('platform:validate-profiles', ['--profile' => 'native'])
        ->expectsOutputToContain('Validation complete for native')
        ->assertExitCode(0);
});
