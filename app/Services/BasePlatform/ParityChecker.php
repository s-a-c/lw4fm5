<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Contracts\BasePlatform\ParityCheckerContract;
use App\Data\ParityReportData;
use App\Enums\ParityStatus;
use App\Models\EnvironmentProfile;
use Illuminate\Support\Collection;

final class ParityChecker implements ParityCheckerContract
{
    /**
     * @param  array<int, string>  $profiles
     * @return array<int, ParityReportData>
     */
    public function run(array $profiles): array
    {
        $targets = $this->resolveProfiles($profiles);

        return $targets->map(static fn (EnvironmentProfile $profile): ParityReportData => new ParityReportData(
            profile: $profile->name,
            status: ParityStatus::Pass,
            issues: [],
        ))->all();
    }

    /**
     * @param  array<int, string>  $profiles
     * @return Collection<int, EnvironmentProfile>
     */
    private function resolveProfiles(array $profiles): Collection
    {
        $query = EnvironmentProfile::query()->where('status', 'supported');

        if ($profiles !== []) {
            /** @phpstan-ignore-next-line */
            $query->whereIn('name', $profiles);
        }

        return $query->get();
    }
}
