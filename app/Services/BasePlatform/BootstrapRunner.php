<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Contracts\BasePlatform\BootstrapRunnerContract;
use App\Data\BootstrapRunData;
use App\Enums\BootstrapStatus;
use App\Support\BasePlatformMetrics;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;

final readonly class BootstrapRunner implements BootstrapRunnerContract
{
    public function __construct(
        private BootstrapRecovery $recovery,
    ) {}

    public function run(string $profile, bool $forceClean): BootstrapRunData
    {
        $supportedRaw = Config::get('base-platform.profiles.supported', []);
        $supported = is_array($supportedRaw) ? $supportedRaw : [];

        throw_unless(in_array($profile, $supported, true), UnsupportedProfileException::class, $profile);

        $startedAt = microtime(true);

        $command = base_path('scripts/platform/bootstrap.sh');

        $result = Process::path(base_path())
            ->timeout(2700)
            ->env([
                'BASE_PLATFORM_PROFILE' => $profile,
                'BASE_PLATFORM_FORCE_CLEAN' => $forceClean ? '1' : '0',
            ])
            ->run($command);

        if ($result->failed()) {
            $guidance = $this->recovery->missingSecret('Flux credentials');
            $errorOutputStr = $result->errorOutput();

            throw new BootstrapRunnerException(
                message: sprintf('Bootstrap failed for %s', $profile),
                output: $errorOutputStr,
                guidance: $guidance,
            );
        }

        $durationMinutes = (microtime(true) - $startedAt) / 60;
        BasePlatformMetrics::recordBootstrapDuration($profile, round($durationMinutes, 2));

        $outputStr = $result->output();

        return new BootstrapRunData(
            profile: $profile,
            status: BootstrapStatus::Success,
            durationMinutes: round($durationMinutes, 2),
            notes: [
                'output' => mb_trim($outputStr),
            ],
        );
    }
}
