<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Contracts\BasePlatform\EnvironmentProfileValidatorContract;
use App\Models\EnvironmentProfile;

final class EnvironmentProfileValidator implements EnvironmentProfileValidatorContract
{
    /**
     * @param  array<int, string>  $profiles
     * @return array<int, ProfileValidationResult>
     */
    public function validate(array $profiles): array
    {
        $targets = $this->resolveProfiles($profiles);

        return $targets->map(static function (EnvironmentProfile $profile): ProfileValidationResult {
            return new ProfileValidationResult(
                profile: $profile->name,
                status: ProfileValidationResult::STATUS_PASS,
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
