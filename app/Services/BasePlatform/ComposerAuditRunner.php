<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Contracts\BasePlatform\ComposerAuditRunnerContract;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

/**
 * Service for running Composer audit commands.
 */
final class ComposerAuditRunner implements ComposerAuditRunnerContract
{
    private const string COMPOSER_AUDIT_COMMAND = 'composer audit --format=json';

    public function run(): ProcessResult
    {
        return Process::run(self::COMPOSER_AUDIT_COMMAND);
    }
}
