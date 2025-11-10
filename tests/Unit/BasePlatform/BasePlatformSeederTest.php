<?php

declare(strict_types=1);

/**
 * Compliant with [AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use Database\Seeders\BasePlatformSeeder;

it('appends timestamp keys without dropping existing attributes', function (): void {
    $seeder = new BasePlatformSeeder();

    $records = [
        [
            'id' => 'example-id',
            'name' => 'example-name',
        ],
    ];

    /** @var list<array<string, mixed>> $result */
    $result = (function (array $payload): array {
        /** @var BasePlatformSeeder $this */
        return $this->appendTimestamps($payload);
    })->call($seeder, $records);

    expect($result)->toHaveCount(1);

    $record = $result[0];

    expect($record)->toHaveKeys(['id', 'name', 'created_at', 'updated_at']);
    expect($record['id'])->toBe('example-id');
    expect($record['name'])->toBe('example-name');
    expect($record['created_at'])->toEqual(now());
    expect($record['updated_at'])->toEqual(now());
});
