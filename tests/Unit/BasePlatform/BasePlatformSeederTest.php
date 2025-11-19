<?php

declare(strict_types=1);

/**
 * Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use Database\Seeders\BasePlatformSeeder;
use Illuminate\Support\Carbon;
use Tests\Unit\BasePlatform\Concerns\InteractsWithBasePlatformSeeder;

uses(InteractsWithBasePlatformSeeder::class);

it('appends timestamp keys without dropping existing attributes', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $frozenNow = Carbon::now();
    Carbon::setTestNow($frozenNow);

    $seeder = new BasePlatformSeeder();

    $records = [
        [
            'id' => 'example-id',
            'name' => 'example-name',
        ],
    ];

    /** @var list<array<string, mixed>> $result */
    /** @phpstan-ignore-next-line */
    $result = ($this->appendTimestamps())($seeder, $records);

    expect($result)->toHaveCount(1);

    $record = $result[0];

    expect($record)->toHaveKeys(['id', 'name', 'created_at', 'updated_at']);
    expect($record['id'])->toBe('example-id');
    expect($record['name'])->toBe('example-name');
    expect($record['created_at'])->toEqual($frozenNow);
    expect($record['updated_at'])->toEqual($frozenNow);

    Carbon::setTestNow();
});
