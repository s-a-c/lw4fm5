<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\BasePlatform\ParityCheckerContract;
use App\Models\EnvironmentProfile;
use App\Models\ParityResult;
use App\Services\BasePlatform\ParityReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

final class RunParityCheck extends Command
{
    protected $signature = 'platform:parity-check
        {--profile= : Limit parity checks to a single profile name}';

    protected $description = 'Compare native and container profiles to detect configuration drift.';

    public function __construct(
        private readonly ParityCheckerContract $checker,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $profiles = $this->determineProfiles();

        if ($profiles === []) {
            $this->components->error('No supported profiles registered. Run the BasePlatformSeeder.');

            return self::FAILURE;
        }

        $reports = $this->checker->run($profiles);

        ParityReport::persistMany($reports)->each(function ($result): void {
            $message = $this->renderStatusMessage($result);

            match ($result->status) {
                ParityReport::STATUS_PASS => $this->components->info($message),
                ParityReport::STATUS_WARNING => $this->components->warn($message),
                default => $this->components->error($message),
            };

            collect($result->issues ?? [])->each(function (string $issue): void {
                $this->line(" - {$issue}");
            });
        });

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function determineProfiles(): array
    {
        $requested = $this->option('profile');

        if (is_string($requested) && $requested !== '') {
            return EnvironmentProfile::query()
                ->supported()
                ->where('name', $requested)
                ->pluck('name')
                ->all();
        }

        return Config::get('base-platform.profiles.supported', []);
    }

    private function renderStatusMessage(ParityResult $result): string
    {
        $profile = $result->environmentProfile->name;

        return match ($result->status) {
            ParityReport::STATUS_PASS => "Parity check passed for {$profile}",
            ParityReport::STATUS_WARNING => "Parity check finished with warnings for {$profile}",
            default => "Parity check failed for {$profile}",
        };
    }
}
