<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Data\BootstrapRecoveryGuidanceData;
use RuntimeException;

final class BootstrapRunnerException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $output,
        public readonly BootstrapRecoveryGuidanceData $guidance,
    ) {
        parent::__construct($message);
    }
}
