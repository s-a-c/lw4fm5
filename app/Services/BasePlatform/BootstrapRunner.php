<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Contracts\BasePlatform\BootstrapRunnerContract;
use App\Support\BasePlatformMetrics;
use Illuminate\Process\Result;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;

final class BootstrapRunner implements BootstrapRunnerContract
{
    public function __construct(
        private readonly BootstrapRecovery $recovery,
    ) {}

    public function run(string $profile, bool $forceClean): BootstrapRun
    {
        $supported = Config::get('base-platform.profiles.supported', []);

        if (! in_array($profile, $supported, true)) {
            throw new UnsupportedProfileException($profile);
        }

        $startedAt = microtime(true);

        $command = base_path('scripts/platform/bootstrap.sh');

        /** @var Result $result */
        $result = Process::path(base_path())
            ->timeout(2700)
            ->env([
                'BASE_PLATFORM_PROFILE' => $profile,
                'BASE_PLATFORM_FORCE_CLEAN' => $forceClean ? '1' : '0',
            ])
            ->run($command);

        if ($result->failed()) {
            $guidance = $this->recovery->missingSecret('Flux credentials');

            throw new BootstrapRunnerException(
                message: sprintf('Bootstrap failed for %s', $profile),
                output: $result->errorOutput(),
                guidance: $guidance,
            );
        }

        $durationMinutes = (microtime(true) - $startedAt) / 60;
        BasePlatformMetrics::recordBootstrapDuration($profile, round($durationMinutes, 2));

        return new BootstrapRun(
            profile: $profile,
            status: BootstrapRun::STATUS_SUCCESS,
            durationMinutes: round($durationMinutes, 2),
            notes: [
                'output' => mb_trim($result->output()),
            ],
        );
    }
}
