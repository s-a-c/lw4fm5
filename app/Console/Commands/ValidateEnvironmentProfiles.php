<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\BasePlatform\EnvironmentProfileValidatorContract;
use App\Models\EnvironmentProfile;
use App\Services\BasePlatform\ProfileValidationResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

final class ValidateEnvironmentProfiles extends Command
{
    protected $signature = 'platform:validate-profiles
        {--profile= : Validate a specific profile}
        {--all : Validate all supported profiles}';

    protected $description = 'Run environment profile validation checks across native and container paths.';

    public function __construct(
        private readonly EnvironmentProfileValidatorContract $validator,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $profiles = $this->determineProfiles();

        if ($profiles === []) {
            $this->components->error('No profiles found to validate. Seed the Base Platform configuration first.');

            return self::FAILURE;
        }

        $results = $this->validator->validate($profiles);

        collect($results)->each(function (ProfileValidationResult $result): void {
            $message = sprintf('Validation complete for %s', $result->profile);

            if ($result->isFail()) {
                $this->components->error($message);
            } elseif ($result->isWarning()) {
                $this->components->warn($message);
            } else {
                $this->components->info($message);
            }

            collect($result->issues)->each(function (string $issue): void {
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
        if ($this->option('all')) {
            $supported = Config::get('base-platform.profiles.supported', []);

            return is_array($supported) ? array_values(array_filter($supported, is_string(...))) : [];
        }

        $profileOption = $this->option('profile');
        $profile = is_string($profileOption) ? $profileOption : '';

        if ($profile !== '') {
            /** @var array<int, string> */
            /** @phpstan-ignore-next-line */
            return EnvironmentProfile::query()
                ->supported()
                ->where('name', $profile)
                ->pluck('name')
                ->all();
        }

        $supported = Config::get('base-platform.profiles.supported', []);

        return is_array($supported) ? array_values(array_filter($supported, is_string(...))) : [];
    }
}
