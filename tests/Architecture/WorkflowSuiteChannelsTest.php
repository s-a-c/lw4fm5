<?php

declare(strict_types=1);

use App\Models\WorkflowSuiteChannel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

it('persists workflow suite notification channels via dedicated table', function (): void {
    $migration = base_path('database/migrations/2025_11_07_000500_create_workflow_suite_channels_table.php');

    expect(File::exists($migration))->toBeTrue();
    expect(class_exists(WorkflowSuiteChannel::class))->toBeTrue();
});

it('defines policy acknowledgement targets in configuration', function (): void {
    $files = Config::get('base-platform.policy.files', []);

    expect($files)->toBeArray();
    /** @phpstan-var array<int, string> $files */
    expect($files)->not->toBeEmpty();
});
