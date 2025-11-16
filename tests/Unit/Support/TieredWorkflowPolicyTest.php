<?php

declare(strict_types=1);

use App\Support\TieredWorkflowPolicy;
use Illuminate\Support\Facades\Config;

it('exposes tiers and resolves workflows for a tier', function (): void {
    Config::set('base-platform.workflow_policy.tiers', [
        'core' => [
            'workflows' => ['tests.yml', 'lint.yml'],
            'checks' => ['lint', 'test', 'type'],
            'sla_minutes' => 25,
        ],
        'heavy' => [
            'workflows' => ['nightly-heavy.yml', 'browser-tests.yml'],
            'checks' => ['mutation', 'browser'],
            'sla_minutes' => 120,
        ],
    ]);

    $tiers = TieredWorkflowPolicy::tiers();
    expect($tiers)->toHaveKeys(['core', 'heavy']);

    $coreWorkflows = TieredWorkflowPolicy::workflowsFor('core');
    expect($coreWorkflows)->toEqual(['tests.yml', 'lint.yml']);

    $none = TieredWorkflowPolicy::workflowsFor('non-existent');
    expect($none)->toBe([]);
});
