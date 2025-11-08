<?php

declare(strict_types=1);

namespace App\Contracts\BasePlatform;

use App\Services\BasePlatform\ProfileValidationResult;

interface EnvironmentProfileValidatorContract
{
    /**
     * @param  array<int, string>  $profiles
     * @return array<int, ProfileValidationResult>
     */
    public function validate(array $profiles): array;
}
