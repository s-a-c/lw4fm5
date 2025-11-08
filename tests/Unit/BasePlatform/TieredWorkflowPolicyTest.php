<?php

declare(strict_types=1);

use App\Support\TieredWorkflowPolicy;

it('exposes tier metadata for workflow policies', function (): void {
    $tiers = TieredWorkflowPolicy::tiers();

    expect($tiers)->toHaveKeys(['core', 'heavy']);

    expect($tiers['core'])->toMatchArray([
        'workflows' => ['tests.yml', 'lint.yml'],
        'checks' => ['lint', 'test', 'type'],
        'sla_minutes' => 25,
    ]);

    expect($tiers['heavy'])->toMatchArray([
        'workflows' => ['nightly-heavy.yml', 'browser-tests.yml'],
        'checks' => ['mutation', 'browser'],
        'sla_minutes' => 120,
    ]);
});
