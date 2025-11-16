<?php

declare(strict_types=1);

use App\Models\WorkflowSuite;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('casts attributes and exposes channels relation', function (): void {
    $model = new WorkflowSuite([
        'name' => 'CI Suite',
        'triggers' => ['push', 'pull_request'],
        'required_checks' => ['lint', 'phpstan'],
        'sla_minutes' => 30,
    ]);

    // Casting assertions
    expect($model->getAttribute('triggers'))
        ->toBeArray()
        ->and($model->getAttribute('required_checks'))
        ->toBeArray()
        ->and($model->getAttribute('sla_minutes'))
        ->toBeInt()
        ->and($model->incrementing)
        ->toBeFalse()
        ->and($model->getKeyType())
        ->toBe('string');

    // Relation type assertion (no DB hit)
    $relation = $model->channels();
    expect($relation)->toBeInstanceOf(HasMany::class);

    // toArray includes our fillable attributes with proper types
    $array = $model->toArray();
    expect($array['name'])->toBe('CI Suite')
        ->and($array['triggers'])->toBe(['push', 'pull_request'])
        ->and($array['required_checks'])->toBe(['lint', 'phpstan'])
        ->and($array['sla_minutes'])->toBe(30);
});
