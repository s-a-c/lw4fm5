<?php

declare(strict_types=1);

use App\Services\BasePlatform\BootstrapRecovery;

it('provides credential onboarding guidance when a secret is missing', function (): void {
    $recovery = new BootstrapRecovery();

    $guidance = $recovery->missingSecret('FLUX_API_TOKEN');

    expect($guidance->title)->toBe('Missing secret: FLUX_API_TOKEN');
    expect($guidance->documentation)->toContain('docs/base-platform/credential-onboarding.md');
    expect($guidance->nextSteps)->toContain('Request access via Platform Engineering for FLUX_API_TOKEN.');
});

it('surfaces offline bootstrap recovery steps', function (): void {
    $recovery = new BootstrapRecovery();

    $guidance = $recovery->offlineMirror();

    expect($guidance->title)->toBe('Offline or proxied environment detected');
    expect($guidance->documentation)->toContain('docs/base-platform/offline-proxy.md');
    expect($guidance->nextSteps)->toContain('Mirror private registries behind your proxy.');
});
