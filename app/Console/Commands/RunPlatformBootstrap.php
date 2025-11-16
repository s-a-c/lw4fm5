<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\BasePlatform\BootstrapRunnerContract;
use App\Services\BasePlatform\BootstrapRecoveryGuidance;
use App\Services\BasePlatform\BootstrapRun;
use App\Services\BasePlatform\BootstrapRunnerException;
use App\Services\BasePlatform\UnsupportedProfileException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

final class RunPlatformBootstrap extends Command
{
    protected $signature = 'platform:bootstrap
        {--profile= : The environment profile to bootstrap}
        {--force-clean : Remove caches and artifacts before bootstrapping}';

    protected $description = 'Run the Base Platform bootstrap workflow for the requested profile.';

    public function __construct(
        private readonly BootstrapRunnerContract $runner,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $profileOption = $this->option('profile');
        $profileDefault = Config::get('base-platform.profiles.supported.0', 'native');
        $profile = is_string($profileOption) ? $profileOption : (is_string($profileDefault) ? $profileDefault : 'native');

        $forceCleanOption = $this->option('force-clean');
        $forceClean = $forceCleanOption === true;

        try {
            $run = $this->runner->run($profile, $forceClean);
        } catch (UnsupportedProfileException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        } catch (BootstrapRunnerException $exception) {
            $this->components->error($exception->getMessage());
            $this->outputGuidance($exception->guidance);

            return self::FAILURE;
        }

        $this->renderSuccessMessage($run);

        return self::SUCCESS;
    }

    private function renderSuccessMessage(BootstrapRun $run): void
    {
        $message = sprintf(
            'Bootstrap complete for %s in %0.2f minutes.',
            $run->profile,
            $run->durationMinutes,
        );

        if ($run->isWarning()) {
            $this->components->warn($message);
        } else {
            $this->components->info($message);
        }
    }

    private function outputGuidance(BootstrapRecoveryGuidance $guidance): void
    {
        $this->components->warn($guidance->title);
        $this->line("Documentation: <info>{$guidance->documentation}</info>");

        foreach ($guidance->nextSteps as $index => $step) {
            $this->line(sprintf('%d. %s', $index + 1, $step));
        }
    }
}
