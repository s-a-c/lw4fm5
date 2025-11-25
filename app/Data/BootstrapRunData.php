<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\BootstrapStatus;
use Spatie\LaravelData\Data;

final class BootstrapRunData extends Data
{
    /**
     * @param  array<string, mixed>  $notes
     */
    public function __construct(
        public string $profile,
        public BootstrapStatus $status,
        public float $durationMinutes,
        public array $notes = [],
    ) {}

    public function isSuccessful(): bool
    {
        return $this->status === BootstrapStatus::Success;
    }

    public function isWarning(): bool
    {
        return $this->status === BootstrapStatus::Warning;
    }
}
