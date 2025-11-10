<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

final readonly class ProfileValidationResult
{
    public const string STATUS_PASS = 'pass';

    public const string STATUS_WARNING = 'warning';

    public const string STATUS_FAIL = 'fail';

    /**
     * @param  list<string>  $issues
     */
    public function __construct(
        public string $profile,
        public string $status,
        public array $issues = [],
    ) {}

    public function isPass(): bool
    {
        return $this->status === self::STATUS_PASS;
    }

    public function isWarning(): bool
    {
        return $this->status === self::STATUS_WARNING;
    }

    public function isFail(): bool
    {
        return $this->status === self::STATUS_FAIL;
    }
}
