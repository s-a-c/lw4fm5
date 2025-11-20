<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class BootstrapRecoveryGuidanceData extends Data
{
    /**
     * @param  list<string>  $nextSteps
     */
    public function __construct(
        public string $title,
        public string $documentation,
        public array $nextSteps,
    ) {}
}
