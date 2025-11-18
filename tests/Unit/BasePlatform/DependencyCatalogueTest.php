<?php

declare(strict_types=1);

use App\Services\BasePlatform\DependencyCatalogue;
use App\Services\BasePlatform\DependencyRecord;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
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

it('throws when catalogue file is missing', function (): void {
    $catalogue = new DependencyCatalogue(Storage::disk('local'));

    expect(fn (): Collection => $catalogue->entries())->toThrow(RuntimeException::class);
});

it('throws when catalogue contains invalid JSON', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', '{invalid json}');

    $catalogue = new DependencyCatalogue(Storage::disk('local'));

    expect(fn (): Collection => $catalogue->entries())->toThrow(JsonException::class);
});

it('throws when catalogue root is not an array', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode('not-an-array', JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));

    expect(fn (): Collection => $catalogue->entries())->toThrow(InvalidArgumentException::class);
});

it('throws when catalogue entry is not an array', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode(['not-an-array-entry'], JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));

    expect(fn (): Collection => $catalogue->entries())->toThrow(InvalidArgumentException::class);
});

it('throws when catalogue entry is missing required keys', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'test/package',
            // Missing other required keys
        ],
    ], JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));

    expect(fn (): Collection => $catalogue->entries())->toThrow(InvalidArgumentException::class);
});

it('throws when catalogue entry has invalid review cadence', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'test/package',
            'version' => '1.0.0',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Test',
            'lastReviewedAt' => '2025-10-10',
            'reviewCadence' => 'invalid-cadence',
            'riskLevel' => 'medium',
            'notes' => 'Test',
        ],
    ], JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));

    expect(fn (): Collection => $catalogue->entries())->toThrow(InvalidArgumentException::class);
});

it('throws when catalogue entry has invalid risk level', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'test/package',
            'version' => '1.0.0',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Test',
            'lastReviewedAt' => '2025-10-10',
            'reviewCadence' => 'monthly',
            'riskLevel' => 'invalid-risk',
            'notes' => 'Test',
        ],
    ], JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));

    expect(fn (): Collection => $catalogue->entries())->toThrow(InvalidArgumentException::class);
});

it('handles non-string values in catalogue entries gracefully', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 123, // Non-string
            'version' => '1.0.0',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Test',
            'lastReviewedAt' => '2025-10-10',
            'reviewCadence' => 'monthly',
            'riskLevel' => 'medium',
            'notes' => 'Test',
        ],
    ], JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));
    $entries = $catalogue->entries();

    expect($entries->first()->name)->toBe(''); // Should default to empty string
});

it('defaults lastReviewedAt to now when value is not a string', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'test/package',
            'version' => '1.0.0',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Test',
            'lastReviewedAt' => ['invalid-date'], // Non-string value
            'reviewCadence' => 'monthly',
            'riskLevel' => 'medium',
            'notes' => 'Test',
        ],
    ], JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));
    $record = $catalogue->entries()->first();

    assert($record !== null);
    expect($record->lastReviewedAt->toDateTimeString())->toBe('2025-11-09 00:00:00');
});

it('allows overdue check with custom reference date', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'test/package',
            'version' => '1.0.0',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Test',
            'lastReviewedAt' => '2025-08-01',
            'reviewCadence' => 'monthly',
            'riskLevel' => 'high',
            'notes' => 'Test',
        ],
    ], JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));
    // Use Carbon::parse() directly to get mutable Carbon (required by overdue() method)
    $customReference = Carbon::parse('2025-12-01');
    $overdue = $catalogue->overdue($customReference);

    expect($overdue)->toHaveCount(1);
});

it('handles isOverdue with CarbonImmutable reference', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'test/package',
            'version' => '1.0.0',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Test',
            'lastReviewedAt' => '2025-08-01',
            'reviewCadence' => 'monthly',
            'riskLevel' => 'high',
            'notes' => 'Test',
        ],
    ], JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));
    $entries = $catalogue->entries();
    $record = $entries->first();

    $carbonImmutableRef = CarbonImmutable::parse('2025-12-01');
    $isOverdue = $record->isOverdue($carbonImmutableRef);

    expect($isOverdue)->toBeTrue();
});

it('converts dependency record to array', function (): void {
    Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
        [
            'name' => 'test/package',
            'version' => '1.0.0',
            'classification' => 'core',
            'owner' => 'Platform Engineering',
            'justification' => 'Test',
            'lastReviewedAt' => '2025-10-10',
            'reviewCadence' => 'monthly',
            'riskLevel' => 'medium',
            'notes' => 'Test notes',
        ],
    ], JSON_THROW_ON_ERROR));

    $catalogue = new DependencyCatalogue(Storage::disk('local'));
    $entries = $catalogue->entries();
    $record = $entries->first();
    $array = $record->toArray();

    expect($array)->toBeArray();
    expect($array['name'])->toBe('test/package');
    expect($array['version'])->toBe('1.0.0');
    expect($array)->toHaveKey('reviewDeadline');
});

it('throws when reviewDeadline encounters unsupported cadence', function (): void {
    // Create a DependencyRecord directly with invalid cadence to test the default case
    // Use Carbon::parse() directly to get mutable Carbon (required by DependencyRecord constructor)
    $record = new DependencyRecord(
        name: 'test/package',
        version: '1.0.0',
        classification: 'core',
        owner: 'Platform Engineering',
        justification: 'Test',
        lastReviewedAt: Date::parse('2025-10-10'),
        reviewCadence: 'unsupported-cadence',
        riskLevel: 'medium',
        notes: 'Test',
    );

    expect(fn (): Carbon => $record->reviewDeadline())->toThrow(InvalidArgumentException::class);
});

it('handles reviewDeadline with CarbonImmutable lastReviewedAt', function (): void {
    // Test line 182: when lastReviewedAt is CarbonImmutable (not Carbon instance)
    $carbonImmutable = CarbonImmutable::parse('2025-10-10');
    $record = new DependencyRecord(
        name: 'test/package',
        version: '1.0.0',
        classification: 'core',
        owner: 'Platform Engineering',
        justification: 'Test',
        lastReviewedAt: $carbonImmutable,
        reviewCadence: 'monthly',
        riskLevel: 'medium',
        notes: 'Test',
    );

    $deadline = $record->reviewDeadline();

    expect($deadline)->toBeInstanceOf(Carbon::class)
        ->and($deadline->toDateString())->toBe('2025-11-10');
});

it('handles reviewDeadline with Carbon lastReviewedAt', function (): void {
    // Test line 181: when lastReviewedAt is Carbon instance (if branch)
    $carbon = Carbon::parse('2025-10-10');
    $record = new DependencyRecord(
        name: 'test/package',
        version: '1.0.0',
        classification: 'core',
        owner: 'Platform Engineering',
        justification: 'Test',
        lastReviewedAt: $carbon,
        reviewCadence: 'monthly',
        riskLevel: 'medium',
        notes: 'Test',
    );

    $deadline = $record->reviewDeadline();

    // Line 181: when lastReviewedAt is Carbon, it uses it directly (before match adds cadence)
    expect($deadline)->toBeInstanceOf(Carbon::class)
        ->and($deadline->toDateString())->toBe('2025-11-10');
});
