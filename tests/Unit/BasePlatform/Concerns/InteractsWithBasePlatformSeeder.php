<?php

declare(strict_types=1);

/**
 * Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
 */

namespace Tests\Unit\BasePlatform\Concerns;

use Closure;
use Database\Seeders\BasePlatformSeeder;
use RuntimeException;

trait InteractsWithBasePlatformSeeder
{
    /**
     * Provides access to the private {@see BasePlatformSeeder::appendTimestamps()} helper.
     *
     * @return Closure(BasePlatformSeeder, list<array<string, mixed>>): list<array<string, mixed>>
     */
    protected function appendTimestamps(): Closure
    {
        return static function (BasePlatformSeeder $seeder, array $records): array {
            $proxy = Closure::bind(
                /**
                 * @param  list<array<string, mixed>>  $payload
                 * @return list<array<string, mixed>>
                 */
                function (array $payload): array {
                    /** @var BasePlatformSeeder $this */
                    return $this->appendTimestamps($payload);
                },
                $seeder,
                BasePlatformSeeder::class,
            );

            throw_if(! $proxy instanceof Closure, RuntimeException::class, 'Unable to access BasePlatformSeeder::appendTimestamps().');

            /** @var list<array<string, mixed>> $result */
            $result = $proxy($records);

            return $result;
        };
    }
}
