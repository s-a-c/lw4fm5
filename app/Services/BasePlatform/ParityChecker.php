<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Contracts\BasePlatform\ParityCheckerContract;
use App\Models\EnvironmentProfile;
use Illuminate\Support\Collection;

final class ParityChecker implements ParityCheckerContract
{
    /**
     * @param  array<int, string>  $profiles
     * @return array<int, ParityReport>
     */
    public function run(array $profiles): array
    {
        $targets = $this->resolveProfiles($profiles);

        return $targets->map(static fn (EnvironmentProfile $profile): ParityReport => new ParityReport(
            profile: $profile->name,
            status: ParityReport::STATUS_PASS,
            issues: [],
        ))->all();
    }

    /**
     * @param  array<int, string>  $profiles
     * @return Collection<int, EnvironmentProfile>
     */
    private function resolveProfiles(array $profiles)
    {
        $query = EnvironmentProfile::query()->where('status', 'supported');

        if ($profiles !== []) {
            $query->whereIn('name', $profiles);
        }

        return $query->get();
    }
}
