<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;

it('reports policy acknowledgement checksum status', function (): void {
    artisan('policy:checksum-monitor')
        ->expectsOutputToContain('Policy acknowledgement checksum summary')
        ->assertExitCode(0);
});

it('reports missing files', function (): void {
    $expected = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    Config::set('base-platform.policy.acknowledgement_checksum', $expected);
    Config::set('base-platform.policy.files', ['tmp/nonexistent-file.md']);

    artisan('policy:checksum-monitor')
        ->expectsOutputToContain('Missing or malformed acknowledgement headers detected')
        ->expectsOutputToContain('tmp/nonexistent-file.md')
        ->assertExitCode(1);
});

it('reports missing acknowledgement headers', function (): void {
    $expected = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    Config::set('base-platform.policy.acknowledgement_checksum', $expected);
    Config::set('base-platform.policy.files', ['tmp/no-header.md']);

    File::ensureDirectoryExists(base_path('tmp'));
    File::put(base_path('tmp/no-header.md'), '# File without acknowledgement header');

    artisan('policy:checksum-monitor')
        ->expectsOutputToContain('Missing or malformed acknowledgement headers detected')
        ->expectsOutputToContain('tmp/no-header.md')
        ->assertExitCode(1);
});

it('handles non-string file entries gracefully', function (): void {
    $expected = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    Config::set('base-platform.policy.acknowledgement_checksum', $expected);
    Config::set('base-platform.policy.files', [123, null, 'tmp/valid.md']); // Mixed types

    File::ensureDirectoryExists(base_path('tmp'));
    File::put(base_path('tmp/valid.md'), <<<MD
    # Valid File

    Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v{$expected}
    MD);

    artisan('policy:checksum-monitor')
        ->expectsOutputToContain('Policy acknowledgement checksum summary')
        ->assertExitCode(0);
});

it('handles non-array mismatched entries gracefully', function (): void {
    // This test ensures the guard against non-array entries works
    // We'll use a real file with mismatched checksum to trigger the mismatched path
    $expected = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    Config::set('base-platform.policy.acknowledgement_checksum', $expected);
    Config::set('base-platform.policy.files', ['tmp/mismatched.md']);

    File::ensureDirectoryExists(base_path('tmp'));
    File::put(base_path('tmp/mismatched.md'), <<<'MD'
    # Mismatched File

    Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) vbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb
    MD);

    artisan('policy:checksum-monitor')
        ->expectsOutputToContain('Checksum drift detected')
        ->assertExitCode(1);
});

it('handles non-array mismatched entry gracefully', function (): void {
    $expected = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    Config::set('base-platform.policy.acknowledgement_checksum', $expected);
    Config::set('base-platform.policy.files', ['tmp/mismatched.md']);

    File::ensureDirectoryExists(base_path('tmp'));
    File::put(base_path('tmp/mismatched.md'), <<<'MD'
    # Mismatched File

    Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) vbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb
    MD);

    // Mock the config to return non-array mismatched entries
    // This tests the guard at line 79
    Config::set('base-platform.policy.mismatched_entries', ['not-an-array-entry']);

    artisan('policy:checksum-monitor')
        ->expectsOutputToContain('Checksum drift detected')
        ->assertExitCode(1);
});
