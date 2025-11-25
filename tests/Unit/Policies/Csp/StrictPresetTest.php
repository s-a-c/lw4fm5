<?php

declare(strict_types=1);

use App\Policies\Csp\StrictPreset;
use Spatie\Csp\Directive;
use Spatie\Csp\Policy;

/**
 * @return array<string, array<int, string>>
 */
function policyDirectives(Policy $policy): array
{
    /** @var array<string, array<int, string>> $directives */
    $directives = (fn (): array => $this->directives)->call($policy);

    return $directives;
}

function withEnvironment(string $env, callable $callback): void
{
    $original = app()->environment();
    app()->instance('env', $env);

    try {
        $callback();
    } finally {
        app()->instance('env', $original);
    }
}

beforeEach(function (): void {
    config(['csp.nonce_enabled' => true]);
    app()->instance('csp-nonce', 'trust-me');
});

test('strict preset configures baseline CSP directives', function (): void {
    withEnvironment('testing', function (): void {
        $policy = new Policy();
        new StrictPreset()->configure($policy);

        $directives = policyDirectives($policy);

        expect($directives[Directive::BASE->value] ?? [])->toContain("'self'")
            ->and($directives[Directive::SCRIPT->value] ?? [])
            ->toContain('strict-dynamic')
            ->toContain("'nonce-trust-me'")
            ->and($directives[Directive::CONNECT->value] ?? [])
            ->not->toContain('ws://localhost:5173');
    });
});

test('local environment allows vite websocket connections', function (): void {
    withEnvironment('local', function (): void {
        $policy = new Policy();
        new StrictPreset()->configure($policy);

        $directives = policyDirectives($policy);

        expect($directives[Directive::CONNECT->value] ?? [])
            ->toContain('ws://localhost:5173');
    });
});
