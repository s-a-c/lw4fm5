<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;

function writePolicyFile(string $relativePath, string $checksum): void
{
    $full = base_path($relativePath);
    File::ensureDirectoryExists(dirname($full));
    $contents = <<<MD
    # Example Doc

    Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v{$checksum}
    MD;
    File::put($full, $contents);
}

it('fails when a file has a mismatched acknowledgement checksum', function (): void {
    // Arrange
    $expected = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'; // 64 hex chars
    Config::set('base-platform.policy.acknowledgement_checksum', $expected);
    Config::set('base-platform.policy.files', ['tmp/policy-a.md']);

    // Write a file with a different checksum
    writePolicyFile('tmp/policy-a.md', 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb');

    // Act + Assert
    artisan('policy:checksum-monitor')
        ->expectsOutputToContain('Policy acknowledgement checksum summary')
        ->expectsOutputToContain('Checksum drift detected')
        ->assertExitCode(1);
});

it('passes when all files match the expected acknowledgement checksum', function (): void {
    // Arrange
    $expected = 'cccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccc'; // 64 hex chars
    Config::set('base-platform.policy.acknowledgement_checksum', $expected);
    Config::set('base-platform.policy.files', ['tmp/policy-b.md']);

    // Write a file that matches checksum
    writePolicyFile('tmp/policy-b.md', $expected);

    // Act + Assert
    artisan('policy:checksum-monitor')
        ->expectsOutputToContain('Policy acknowledgement checksum summary')
        ->assertExitCode(0);
});
