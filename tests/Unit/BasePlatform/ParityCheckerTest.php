<?php

declare(strict_types=1);

use App\Models\EnvironmentProfile;
use App\Services\BasePlatform\ParityChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns pass reports for all supported profiles', function (): void {
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

    $checker = app(ParityChecker::class);
    $reports = $checker->run([]);

    expect($reports)->toHaveCount(2)
        ->and(collect($reports)->pluck('profile')->all())
        ->toEqualCanonicalizing(['native', 'container'])
        ->and(collect($reports)->pluck('status')->unique()->all())
        ->toEqual(['pass']);
});

it('filters reports by provided profile names', function (): void {
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

    $checker = app(ParityChecker::class);
    $reports = $checker->run(['container']);

    expect($reports)->toHaveCount(1)
        ->and($reports[0]->profile)->toBe('container');
});
