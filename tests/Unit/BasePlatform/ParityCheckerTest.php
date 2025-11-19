<?php

declare(strict_types=1);

use App\Models\EnvironmentProfile;
use App\Services\BasePlatform\ParityChecker;
use App\Services\BasePlatform\ParityReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

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

    /** @phpstan-var array<int, ParityReport> $reports */
    expect($reports)->toHaveCount(2);
    /** @phpstan-var Collection<int, string> $profiles */
    $profiles = collect($reports)->pluck('profile');
    expect($profiles->all())->toEqualCanonicalizing(['native', 'container']);
    /** @phpstan-var Collection<int, string> $statuses */
    $statuses = collect($reports)->pluck('status')->unique();
    expect($statuses->all())->toEqual(['pass']);
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
