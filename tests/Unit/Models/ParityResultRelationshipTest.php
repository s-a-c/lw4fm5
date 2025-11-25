<?php

declare(strict_types=1);

use App\Models\EnvironmentProfile;
use App\Models\ParityResult;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has environmentProfile relationship', function (): void {
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

    $result->refresh();

    // Verify the foreign key is set
    $foreignKey = $result->getAttribute('environment_profile_id');
    expect($foreignKey)->toBe($profile->id);
    expect($foreignKey)->not->toBeNull();

    // Verify the profile exists
    $profileCheck = EnvironmentProfile::query()->find($profile->id);
    expect($profileCheck)->not->toBeNull();

    // Access the relationship using the query builder
    /** @var BelongsTo<EnvironmentProfile, ParityResult> $relationship */
    $relationship = $result->environmentProfile();
    /** @var EnvironmentProfile|null $environmentProfile */
    $environmentProfile = $relationship->first();
    expect($environmentProfile)->not->toBeNull();
    expect($environmentProfile->id)->toBe($profile->id);
    expect($environmentProfile->name)->toBe('native');

    // Also verify dynamic property access works
    $environmentProfile2 = $result->environmentProfile;
    expect($environmentProfile2)->not->toBeNull();
    expect($environmentProfile2->id)->toBe($profile->id);
});
