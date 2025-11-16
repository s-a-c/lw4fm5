<?php

declare(strict_types=1);

namespace App\Contracts\BasePlatform;

use Illuminate\Contracts\Process\ProcessResult;

/**
 * Contract for running Composer audit commands.
 */
interface ComposerAuditRunnerContract
{
    /**
     * Run composer audit command and return the result.
     */
    public function run(): ProcessResult;
}
