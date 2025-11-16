<?php

declare(strict_types=1);

use App\Models\EnvironmentProfile;
use App\Services\BasePlatform\EnvironmentProfileValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns all supported profiles when none are specified', function (): void {
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

    EnvironmentProfile::query()->create([
        'name' => 'legacy',
        'runtime_versions' => ['php' => '8.2'],
        'prerequisites' => [],
        'smoke_check_script' => null,
        'status' => 'deprecated',
    ]);

    $validator = app(EnvironmentProfileValidator::class);
    $results = $validator->validate([]);

    // Only the 2 supported profiles should be returned
    expect($results)->toHaveCount(2)
        ->and(collect($results)->pluck('profile')->all())
        ->toEqualCanonicalizing(['native', 'container']);
});

it('filters by provided profile names', function (): void {
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

    $validator = app(EnvironmentProfileValidator::class);
    $results = $validator->validate(['native']);

    expect($results)->toHaveCount(1)
        ->and($results[0]->profile)->toBe('native');
});
