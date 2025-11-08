<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

final class BootstrapRun
{
    public const STATUS_SUCCESS = 'success';

    public const STATUS_WARNING = 'warning';

    public const STATUS_FAILED = 'failed';

    /**
     * @param  array<string, mixed>  $notes
     */
    public function __construct(
        public readonly string $profile,
        public readonly string $status,
        public readonly float $durationMinutes,
        public readonly array $notes = [],
    ) {}

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isWarning(): bool
    {
        return $this->status === self::STATUS_WARNING;
    }
}
