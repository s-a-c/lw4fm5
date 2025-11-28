<?php

declare(strict_types=1);

namespace Tests\Feature\Theme;

use App\Livewire\Settings\Appearance;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Mockery;

/**
 * Test telemetry anonymization (T027k, FR-037).
 *
 * Verifies that theme-related telemetry does not expose PII:
 * - No email addresses
 * - No passwords
 * - No names
 * - Only user IDs (non-sensitive identifier)
 * - No sensitive request data
 */
test('theme events do not log email addresses', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    Log::shouldReceive('info')
        ->atLeast()->once()
        ->with(Mockery::on(fn ($message): bool => in_array($message, ['Theme changed', 'Theme preference changed'], true)), Mockery::on(function (array $context): true {
            // Verify email is NOT in context
            $contextString = json_encode($context);
            expect($contextString)->not->toContain('test@example.com');
            expect($contextString)->not->toContain('@example.com');

            // Verify user_id is present (non-sensitive)
            expect($context)->toHaveKey('user_id');
            expect($context['user_id'])->toBeInt();

            return true;
        }));

    Log::shouldReceive('warning')->zeroOrMoreTimes();
    Log::shouldReceive('debug')->zeroOrMoreTimes();

    Livewire::actingAs($user)
        ->test(Appearance::class)
        ->set('theme', 'kanagawa')
        ->call('performSave');
});

test('theme events do not log passwords', function (): void {
    $user = User::factory()->create([
        'password' => 'secret-password-123',
    ]);

    Log::shouldReceive('info')
        ->atLeast()->once()
        ->with(Mockery::on(fn ($message): bool => in_array($message, ['Theme changed', 'Theme preference changed'], true)), Mockery::on(function ($context): true {
            $contextString = json_encode($context);
            // Verify password is NOT in context
            expect($contextString)->not->toContain('secret-password');
            expect($contextString)->not->toContain('password');

            return true;
        }));

    Log::shouldReceive('warning')->zeroOrMoreTimes();
    Log::shouldReceive('debug')->zeroOrMoreTimes();

    Livewire::actingAs($user)
        ->test(Appearance::class)
        ->set('theme', 'kanagawa')
        ->call('performSave');
});

test('theme events do not log user names', function (): void {
    $user = User::factory()->create([
        'name' => 'John Doe',
    ]);

    Log::shouldReceive('info')
        ->atLeast()->once()
        ->with(Mockery::on(fn ($message): bool => in_array($message, ['Theme changed', 'Theme preference changed'], true)), Mockery::on(function ($context): true {
            $contextString = json_encode($context);
            // Verify name is NOT in context
            expect($contextString)->not->toContain('John Doe');
            expect($contextString)->not->toContain('John');

            // Verify user_id is present (non-sensitive)
            expect($context)->toHaveKey('user_id');

            return true;
        }));

    Log::shouldReceive('warning')->zeroOrMoreTimes();
    Log::shouldReceive('debug')->zeroOrMoreTimes();

    Livewire::actingAs($user)
        ->test(Appearance::class)
        ->set('theme', 'kanagawa')
        ->call('performSave');
});

test('theme error logs do not expose sensitive data', function (): void {
    $user = User::factory()->create([
        'email' => 'sensitive@example.com',
        'name' => 'Sensitive User',
        'password' => 'sensitive-password',
    ]);

    Log::shouldReceive('warning')
        ->atLeast()->once()
        ->with(Mockery::type('string'), Mockery::on(function (array $context): true {
            $contextString = json_encode($context);
            // Verify no sensitive data
            expect($contextString)->not->toContain('sensitive@example.com');
            expect($contextString)->not->toContain('Sensitive User');
            expect($contextString)->not->toContain('sensitive-password');

            // Verify only user_id is present
            if (isset($context['user_id'])) {
                expect($context['user_id'])->toBeInt();
            }

            return true;
        }));

    // Trigger a validation correction (which logs a warning)
    Livewire::actingAs($user)
        ->test(Appearance::class)
        ->set('theme', 'kanagawa')
        ->set('flavor', 'frappe') // Invalid flavor for Kanagawa
        ->call('performSave');
});

test('preview interaction logs do not expose user PII', function (): void {
    $user = User::factory()->create([
        'email' => 'preview@example.com',
        'name' => 'Preview User',
    ]);

    Log::shouldReceive('info')
        ->once()
        ->with('Preview page interaction', Mockery::on(function (array $context): true {
            $contextString = json_encode($context);
            // Verify no sensitive data
            expect($contextString)->not->toContain('preview@example.com');
            expect($contextString)->not->toContain('Preview User');

            // Verify user_id is present if authenticated
            if (isset($context['user_id'])) {
                expect($context['user_id'])->toBeInt();
            }

            return true;
        }));

    $this->actingAs($user)
        ->postJson('/themes/preview/interaction', [
            'interaction_type' => 'theme_change',
            'interaction_value' => 'kanagawa',
            'theme' => 'kanagawa',
            'flavor' => 'wave',
            'accent' => 'primary',
        ]);
});

test('theme performance tracker does not log PII', function (): void {
    $user = User::factory()->create([
        'email' => 'perf@example.com',
        'name' => 'Performance User',
    ]);

    // Mock all log calls that might occur
    Log::shouldReceive('info')->zeroOrMoreTimes();
    Log::shouldReceive('warning')->zeroOrMoreTimes();
    Log::shouldReceive('debug')
        ->atLeast()->once()
        ->with('Theme performance marker', Mockery::on(function ($context): true {
            $contextString = json_encode($context);
            // Verify no sensitive data
            expect($contextString)->not->toContain('perf@example.com');
            expect($contextString)->not->toContain('Performance User');

            // Verify only operation and performance metrics
            expect($context)->toHaveKey('operation');
            expect($context)->toHaveKey('dom_update_time_ms');

            return true;
        }));

    // Trigger a theme save which will call ThemePerformanceTracker
    Livewire::actingAs($user)
        ->test(Appearance::class)
        ->set('theme', 'kanagawa')
        ->call('performSave');
});
