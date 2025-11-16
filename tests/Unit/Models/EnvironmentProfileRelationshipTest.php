<?php

declare(strict_types=1);

use App\Models\EnvironmentProfile;
use App\Models\ParityResult;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has parityResults relationship', function (): void {
    $profile = EnvironmentProfile::query()->create([
        'name' => 'native',
        'runtime_versions' => ['php' => '8.4'],
        'prerequisites' => [],
        'smoke_check_script' => null,
        'status' => 'supported',
    ]);
    $result = ParityResult::query()->create([
        'environment_profile_id' => $profile->id,
        'run_date' => now(),
        'status' => 'pass',
        'issues' => [],
    ]);

    expect($profile->parityResults)->toHaveCount(1);
    expect($profile->parityResults->first()->id)->toBe($result->id);
});
