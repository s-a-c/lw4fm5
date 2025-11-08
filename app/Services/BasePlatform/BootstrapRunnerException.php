<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use RuntimeException;

final class BootstrapRunnerException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $output,
        public readonly BootstrapRecoveryGuidance $guidance,
    ) {
        parent::__construct($message);
    }
}
