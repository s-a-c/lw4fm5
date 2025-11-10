<?php

declare(strict_types=1);

use App\Services\BasePlatform\DependencyCatalogue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('local');
    Date::setTestNow('2025-11-09 12:00:00');
});

afterEach(function (): void {
    Date::setTestNow();
});

it('parses the dependency catalogue and flags overdue reviews', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'laravel/framework',
            'version' => '12.0.0',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Application runtime and foundation',
            'lastReviewedAt' => '2025-08-01',
            'reviewCadence' => 'monthly',
            'riskLevel' => 'high',
            'notes' => 'Pinned to LTS release',
        ],
        [
            'name' => 'spatie/laravel-enum',
            'version' => '5.5.1',
            'classification' => 'optional',
            'owner' => 'Platform Engineering',
            'justification' => 'Provides typed enums used by workflow configuration',
            'lastReviewedAt' => '2025-10-15',
            'reviewCadence' => 'quarterly',
            'riskLevel' => 'low',
            'notes' => 'Optional helper; keep parity with model casts',
        ],
    ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));

    $entries = $catalogue->entries();

    expect($entries)->toHaveCount(2);
    expect($entries->first()->name)->toBe('laravel/framework');
    expect($entries->first()->classification)->toBe('core');

    $overdue = $catalogue->overdue();

    expect($overdue)->toHaveCount(1);
    expect($overdue->first()->name)->toBe('laravel/framework');
});

it('rejects catalogue entries with unsupported metadata', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'invalid/package',
            'version' => '1.0.0',
            'classification' => 'unsupported',
            'owner' => 'Platform Engineering',
            'justification' => 'Test fixture',
            'lastReviewedAt' => '2025-10-10',
            'reviewCadence' => 'monthly',
            'riskLevel' => 'medium',
            'notes' => 'Invalid classification should fail validation',
        ],
    ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));

    expect(fn (): Collection => $catalogue->entries())->toThrow(InvalidArgumentException::class);
});
