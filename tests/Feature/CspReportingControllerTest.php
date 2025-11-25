<?php

declare(strict_types=1);

use App\Models\CspViolation;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

test('csp reports are persisted', function (): void {
    $report = [
        'blocked-uri' => 'https://evil.example/script.js',
        'document-uri' => 'https://app.test/dashboard',
        'violated-directive' => 'script-src-elem',
        'original-policy' => "default-src 'self';",
    ];

    postJson(route('csp.report'), ['csp-report' => $report])
        ->assertNoContent();

    expect(CspViolation::query()->count())->toBe(1);

    /** @var CspViolation $saved */
    $saved = CspViolation::query()->firstOrFail();

    expect($saved->blocked_uri)->toBe($report['blocked-uri']);
    expect($saved->document_uri)->toBe($report['document-uri']);
    expect($saved->violated_directive)->toBe($report['violated-directive']);
    expect($saved->original_policy)->toBe($report['original-policy']);
    expect($saved->ip_address)->toBe('127.0.0.1');
});

test('missing reports are ignored', function (): void {
    postJson(route('csp.report'), [])->assertNoContent();

    expect(CspViolation::query()->count())->toBe(0);
});
