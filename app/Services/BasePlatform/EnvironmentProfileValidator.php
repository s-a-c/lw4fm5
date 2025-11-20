<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Contracts\BasePlatform\EnvironmentProfileValidatorContract;
use App\Data\ProfileValidationResultData;
use App\Enums\ValidationStatus;
use App\Models\EnvironmentProfile;
use Illuminate\Support\Collection;

final class EnvironmentProfileValidator implements EnvironmentProfileValidatorContract
{
    /**
     * @param  array<int, string>  $profiles
     * @return array<int, ProfileValidationResultData>
     */
    public function validate(array $profiles): array
    {
        $targets = $this->resolveProfiles($profiles);

        return $targets->map(static fn (EnvironmentProfile $profile): ProfileValidationResultData => new ProfileValidationResultData(
            profile: $profile->name,
            status: ValidationStatus::Pass,
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
