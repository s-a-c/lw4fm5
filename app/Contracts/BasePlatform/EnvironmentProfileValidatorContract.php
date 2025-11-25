<?php

declare(strict_types=1);

namespace App\Contracts\BasePlatform;

use App\Data\ProfileValidationResultData;

interface EnvironmentProfileValidatorContract
{
    /**
     * @param  array<int, string>  $profiles
     * @return array<int, ProfileValidationResultData>
     */
    public function validate(array $profiles): array;
}
