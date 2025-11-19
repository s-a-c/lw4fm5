<?php

declare(strict_types=1);

use App\Models\WorkflowSuiteChannel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('exposes workflowSuite relation and basic attributes', function (): void {
    $model = new WorkflowSuiteChannel([
        'workflow_suite_id' => 'suite-123',
        'channel' => 'slack',
        'medium' => 'chat',
    ]);

    // Config
    expect($model->incrementing)->toBeFalse()
        ->and($model->getKeyType())->toBe('string');

    // Relation type assertion (no DB hit)
    $relation = $model->workflowSuite();
    expect($relation)->toBeInstanceOf(BelongsTo::class);

    // toArray includes our fillable attributes
    $array = $model->toArray();
    expect($array['workflow_suite_id'])->toBe('suite-123')
        ->and($array['channel'])->toBe('slack')
        ->and($array['medium'])->toBe('chat');
});
