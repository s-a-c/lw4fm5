<?php

declare(strict_types=1);

use App\Models\ToolchainDefinition;

it('casts attributes and basic configuration is correct', function (): void {
    $model = new ToolchainDefinition([
        'language' => 'php',
        'version' => '8.4',
        'enforcement_scope' => 'ci',
        'verification_command' => 'php -v',
        'documentation_url' => 'https://example.com/docs',
    ]);

    expect($model->incrementing)->toBeFalse()
        ->and($model->getKeyType())->toBe('string');

    // Casting assertion
    expect($model->getAttribute('documentation_url'))
        ->toBe('https://example.com/docs');

    // toArray includes fillable attributes
    $array = $model->toArray();
    expect($array['language'])->toBe('php')
        ->and($array['version'])->toBe('8.4')
        ->and($array['enforcement_scope'])->toBe('ci')
        ->and($array['verification_command'])->toBe('php -v')
        ->and($array['documentation_url'])->toBe('https://example.com/docs');
});
