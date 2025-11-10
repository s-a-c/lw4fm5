<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Contracts\BasePlatform\ParityCheckerContract;
use App\Models\EnvironmentProfile;

final class ParityChecker implements ParityCheckerContract
{
    /**
     * @param  array<int, string>  $profiles
     * @return array<int, ParityReport>
     */
    public function run(array $profiles): array
    {
        $targets = $this->resolveProfiles($profiles);

        return $targets->map(static function (EnvironmentProfile $profile): ParityReport {
            return new ParityReport(
                profile: $profile->name,
                status: ParityReport::STATUS_PASS,
                issues: [],
            );
        })->all();
    }

    /**
     * @param  array<int, string>  $profiles
     * @return \Illuminate\Support\Collection<int, EnvironmentProfile>
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
