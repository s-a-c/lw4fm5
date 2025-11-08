<?php

declare(strict_types=1);

use App\Contracts\BasePlatform\EnvironmentProfileValidatorContract;
use App\Models\EnvironmentProfile;
use App\Services\BasePlatform\ProfileValidationResult;
use Illuminate\Support\Str;

use function Pest\Laravel\artisan;
use function Pest\Laravel\mock;

it('validates the container profile exclusively when requested', function (): void {
    EnvironmentProfile::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'native',
        'runtime_versions' => ['php' => '8.5', 'bun' => '1.1'],
        'prerequisites' => ['herd' => true],
        'smoke_check_script' => 'tests/smoke/native.sh',
        'status' => 'supported',
    ]);

    EnvironmentProfile::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'container',
        'runtime_versions' => ['php' => '8.5', 'bun' => '1.1'],
        'prerequisites' => ['docker' => true],
        'smoke_check_script' => 'tests/smoke/container.sh',
        'status' => 'supported',
    ]);

    mock(EnvironmentProfileValidatorContract::class)
        ->shouldReceive('validate')
        ->once()
        ->with(['container'])
        ->andReturn([
            new ProfileValidationResult(
                profile: 'container',
                status: ProfileValidationResult::STATUS_WARNING,
                issues: ['queue worker lag detected'],
            ),
        ]);

    artisan('platform:validate-profiles', ['--profile' => 'container'])
        ->expectsOutputToContain('Validation complete for container')
        ->expectsOutputToContain('queue worker lag detected')
        ->assertExitCode(0);
});
