<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\ValidationStatus;
use Spatie\LaravelData\Data;

final class ProfileValidationResultData extends Data
{
    /**
     * @param  list<string>  $issues
     */
    public function __construct(
        public string $profile,
        public ValidationStatus $status,
        public array $issues = [],
    ) {}

    public function isPass(): bool
    {
        return $this->status === ValidationStatus::Pass;
    }

    public function isWarning(): bool
    {
        return $this->status === ValidationStatus::Warning;
    }

    public function isFail(): bool
    {
        return $this->status === ValidationStatus::Fail;
    }
}
