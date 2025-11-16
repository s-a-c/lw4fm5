<?php

declare(strict_types=1);

use App\Models\WorkflowSuite;
use App\Models\WorkflowSuiteChannel;
use App\Services\BasePlatform\WorkflowSuiteChannelSync;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

it('creates desired channels and removes stale ones', function (): void {
    // Arrange suites
    /** @var WorkflowSuite $suite */
    $suite = WorkflowSuite::query()->create([
        'name' => 'core-quality',
        'triggers' => ['push'],
        'required_checks' => ['lint'],
        'sla_minutes' => 15,
    ]);

    /** @var WorkflowSuite $other */
    $other = WorkflowSuite::query()->create([
        'name' => 'heavy-quality',
        'triggers' => ['schedule'],
        'required_checks' => ['mutation'],
        'sla_minutes' => 120,
    ]);

    // Seed an existing stale channel for the first suite that is NOT in desired config
    $stale = WorkflowSuiteChannel::query()->create([
        'workflow_suite_id' => $suite->id,
        'channel' => 'stale::#old',
        'medium' => 'slack',
    ]);

    // Desired config contains 2 channels for core-quality, and none for heavy-quality
    Config::set('base-platform.workflow_suite_channels', [
        'core-quality' => [
            ['channel' => 'slack::#ci-core-quality', 'medium' => 'slack'],
            ['channel' => 'email::platform-alerts@example.com', 'medium' => 'email'],
        ],
    ]);

    // Act: run sync
    app(WorkflowSuiteChannelSync::class)->sync();

    // Assert: two desired channels exist for core-quality
    $coreChannels = WorkflowSuiteChannel::query()
        ->where('workflow_suite_id', $suite->id)
        ->get();

    expect($coreChannels)->toHaveCount(2)
        ->and($coreChannels->pluck('channel')->all())
        ->toEqualCanonicalizing([
            'slack::#ci-core-quality',
            'email::platform-alerts@example.com',
        ]);

    // Stale channel should be removed
    expect(WorkflowSuiteChannel::query()->find($stale->id))->toBeNull();

    // No channels should be created for suites not present in config
    $heavyChannels = WorkflowSuiteChannel::query()
        ->where('workflow_suite_id', $other->id)
        ->count();

    expect($heavyChannels)->toBe(0);
});

it('skips suites that do not exist in database', function (): void {
    // Config references a suite that doesn't exist in DB
    Config::set('base-platform.workflow_suite_channels', [
        'non-existent-suite' => [
            ['channel' => 'slack::#test', 'medium' => 'slack'],
        ],
    ]);

    // Should not throw, should skip gracefully
    app(WorkflowSuiteChannelSync::class)->sync();

    // No channels should be created
    expect(WorkflowSuiteChannel::query()->count())->toBe(0);
});
