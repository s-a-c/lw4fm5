<?php

declare(strict_types=1);

use App\Models\CredentialPolicy;
use Illuminate\Support\Str;

it('casts rotation interval to an integer', function (): void {
    $policy = CredentialPolicy::query()->create([
        'id' => (string) Str::uuid(),
        'context' => 'ci',
        'storage_mechanism' => 'github_actions_secret',
        'rotation_interval_days' => '90',
        'owner' => 'Platform Engineering',
        'notes' => null,
    ]);

    expect($policy->rotation_interval_days)->toBeInt()->toBe(90);
});

it('filters policies by context using the query scope', function (): void {
    CredentialPolicy::query()->insert([
        [
            'id' => (string) Str::uuid(),
            'context' => 'ci',
            'storage_mechanism' => 'github_actions_secret',
            'rotation_interval_days' => 90,
            'owner' => 'Platform Engineering',
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id' => (string) Str::uuid(),
            'context' => 'local',
            'storage_mechanism' => 'encrypted_env_file',
            'rotation_interval_days' => 120,
            'owner' => 'Solo Developer',
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $ciPolicies = CredentialPolicy::forContext('ci')->pluck('context')->all();

    expect($ciPolicies)->toBe(['ci']);
});
