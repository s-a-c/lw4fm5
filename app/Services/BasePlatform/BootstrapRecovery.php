<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use Illuminate\Support\Str;

final class BootstrapRecovery
{
    public function missingSecret(string $key): BootstrapRecoveryGuidance
    {
        $normalized = Str::upper($key);

        return new BootstrapRecoveryGuidance(
            title: "Missing secret: {$normalized}",
            documentation: 'docs/base-platform/credential-onboarding.md',
            nextSteps: [
                "Request access via Platform Engineering for {$normalized}.",
                'Store the secret in the encrypted local .env file and GitHub Actions secrets.',
                'Rerun the bootstrap command once credentials are in place.',
            ],
        );
    }

    public function offlineMirror(): BootstrapRecoveryGuidance
    {
        return new BootstrapRecoveryGuidance(
            title: 'Offline or proxied environment detected',
            documentation: 'docs/base-platform/offline-proxy.md',
            nextSteps: [
                'Mirror private registries behind your proxy.',
                'Run smoke tests using mirrored assets.',
                'Escalate to Platform Engineering if mirrored artifacts fail parity checks.',
            ],
        );
    }
}
