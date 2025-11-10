<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

/**
 * @internal Value object returned by BootstrapRecovery helper.
 *
 * @param  array<int, string>  $nextSteps
 */
final readonly class BootstrapRecoveryGuidance
{
    /**
     * @param  array<int, string>  $nextSteps
     */
    public function __construct(
        public string $title,
        public string $documentation,
        public array $nextSteps,
    ) {}
}
