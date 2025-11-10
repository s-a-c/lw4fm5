<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

final readonly class BootstrapRun
{
    public const string STATUS_SUCCESS = 'success';

    public const string STATUS_WARNING = 'warning';

    public const string STATUS_FAILED = 'failed';

    /**
     * @param  array<string, mixed>  $notes
     */
    public function __construct(
        public string $profile,
        public string $status,
        public float $durationMinutes,
        public array $notes = [],
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
