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

        ParityReport::persistMany($reports)->each(function (ParityResult $result): void {
            $message = $this->renderStatusMessage($result);

            match ($result->status) {
                ParityReport::STATUS_PASS => $this->components->info($message),
                ParityReport::STATUS_WARNING => $this->components->warn($message),
                default => $this->components->error($message),
            };

            $issues = $result->issues ?? [];
            if (is_array($issues)) {
                collect($issues)->each(function (mixed $issue): void {
                    if (is_string($issue)) {
                        $this->line(" - {$issue}");
                    }
                });
            }
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
            /** @phpstan-ignore-next-line */
            $query = EnvironmentProfile::query()->supported();
            /** @var array<int, string> $result */
            $result = $query
                ->where('name', $requested)
                ->pluck('name')
                ->all();

            return $result;
        }

        $supported = Config::get('base-platform.profiles.supported', []);

        return is_array($supported) ? array_values(array_filter($supported, is_string(...))) : [];
    }

    private function renderStatusMessage(ParityResult $result): string
    {
        $environmentProfile = $result->environmentProfile;
        $profile = $environmentProfile->name ?? 'unknown';

        return match ($result->status) {
            ParityReport::STATUS_PASS => "Parity check passed for {$profile}",
            ParityReport::STATUS_WARNING => "Parity check finished with warnings for {$profile}",
            default => "Parity check failed for {$profile}",
        };
    }
}
