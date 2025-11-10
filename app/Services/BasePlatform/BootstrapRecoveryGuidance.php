<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

/**
 * @internal Value object returned by BootstrapRecovery helper.
 *
 * @param  array<int, string>  $nextSteps
 */
final class BootstrapRecoveryGuidance
{
    /**
     * @param  array<int, string>  $nextSteps
     */
    public function __construct(
        public readonly string $title,
        public readonly string $documentation,
        public readonly array $nextSteps,
    ) {}
}
