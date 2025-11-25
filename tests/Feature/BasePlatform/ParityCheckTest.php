<?php

declare(strict_types=1);

use App\Contracts\BasePlatform\ParityCheckerContract;
use App\Data\ParityReportData;
use App\Enums\ParityStatus;
use App\Models\EnvironmentProfile;
use App\Models\ParityResult;
use Illuminate\Support\Str;
use Mockery\MockInterface;

use function Pest\Laravel\artisan;
use function Pest\Laravel\mock;

it('records a passing parity report for a specific profile', function (): void {
    $profile = EnvironmentProfile::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'native',
        'runtime_versions' => ['php' => '8.5', 'bun' => '1.1'],
        'prerequisites' => ['herd' => true],
        'smoke_check_script' => 'tests/smoke/native.sh',
        'status' => 'supported',
    ]);

    /** @phpstan-var MockInterface&ParityCheckerContract $mock */
    $mock = mock(ParityCheckerContract::class);
    $mock->shouldReceive('run')
        ->once()
        ->with(['native'])
        ->andReturn([
            new ParityReportData(
                profile: 'native',
                status: ParityStatus::Pass,
                issues: []
            ),
        ]);

    app()->instance(ParityCheckerContract::class, $mock);

    artisan('platform:parity-check', ['--profile' => 'native'])
        ->expectsOutputToContain('Parity check passed for native')
        ->assertExitCode(0);

    expect(
        ParityResult::query()
            ->where('environment_profile_id', $profile->id)
            ->where('status', 'pass')
            ->count()
    )->toBe(1);
});
