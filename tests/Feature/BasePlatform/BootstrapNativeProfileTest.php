<?php

declare(strict_types=1);

use App\Contracts\BasePlatform\EnvironmentProfileValidatorContract;
use App\Data\ProfileValidationResultData;
use App\Enums\ValidationStatus;
use App\Models\EnvironmentProfile;
use Illuminate\Support\Str;
use Mockery\MockInterface;

use function Pest\Laravel\artisan;
use function Pest\Laravel\mock;

it('validates only the native profile when requested', function (): void {
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

    /** @phpstan-var MockInterface&EnvironmentProfileValidatorContract $mock */
    $mock = mock(EnvironmentProfileValidatorContract::class);
    $mock->shouldReceive('validate')
        ->once()
        ->with(['native'])
        ->andReturn([
            new ProfileValidationResultData(
                profile: 'native',
                status: ValidationStatus::Pass,
                issues: []
            ),
        ]);

    app()->instance(EnvironmentProfileValidatorContract::class, $mock);

    artisan('platform:validate-profiles', ['--profile' => 'native'])
        ->expectsOutputToContain('Validation complete for native')
        ->assertExitCode(0);
});
