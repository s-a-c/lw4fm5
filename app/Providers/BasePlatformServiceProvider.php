<?php

declare(strict_types=1);

namespace App\Providers;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use App\Contracts\BasePlatform\BootstrapRunnerContract;
use App\Contracts\BasePlatform\ComposerAuditRunnerContract;
use App\Contracts\BasePlatform\EnvironmentProfileValidatorContract;
use App\Contracts\BasePlatform\ParityCheckerContract;
use App\Services\BasePlatform\BootstrapRunner;
use App\Services\BasePlatform\ComposerAuditRunner;
use App\Services\BasePlatform\EnvironmentProfileValidator;
use App\Services\BasePlatform\ParityChecker;
use Illuminate\Support\ServiceProvider;

final class BasePlatformServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BootstrapRunnerContract::class, BootstrapRunner::class);
        $this->app->bind(ComposerAuditRunnerContract::class, ComposerAuditRunner::class);
        $this->app->bind(ParityCheckerContract::class, ParityChecker::class);
        $this->app->bind(EnvironmentProfileValidatorContract::class, EnvironmentProfileValidator::class);
    }

    public function boot(): void
    {
        //
    }
}
