<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

final class ProfileValidationResult
{
    public const STATUS_PASS = 'pass';

    public const STATUS_WARNING = 'warning';

    public const STATUS_FAIL = 'fail';

    /**
     * @param  list<string>  $issues
     */
    public function __construct(
        public readonly string $profile,
        public readonly string $status,
        public readonly array $issues = [],
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
