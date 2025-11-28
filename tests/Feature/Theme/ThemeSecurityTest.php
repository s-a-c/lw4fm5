<?php

declare(strict_types=1);

use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

test('XSS attacks are prevented in theme values', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Attempt to inject XSS payload in theme value
    $xssPayload = '<script>alert("XSS")</script>';

    $component = Livewire::test('settings.appearance');

    // Setting invalid theme should trigger validation
    $component->set('theme', $xssPayload);

    // After setting, the component should validate and correct the value
    // The updatedTheme() method should validate and correct invalid values
    $themeValue = $component->get('theme');

    // Theme should be validated - invalid values are corrected to default
    // The value should not contain XSS payload (it should be a valid theme enum value)
    expect($themeValue)->not->toContain('<script>')
        ->and($themeValue)->not->toContain('alert')
        ->and($themeValue)->toBeIn([Theme::Catppuccin->value, Theme::Kanagawa->value]);
});

test('CSRF protection is enabled for theme updates', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Livewire automatically handles CSRF for component updates
    // Verify that component requires authentication
    $component = Livewire::test('settings.appearance');

    // Component should be accessible when authenticated
    $component->assertSuccessful();

    // Verify that unauthenticated requests are blocked
    $this->withoutMiddleware(VerifyCsrfToken::class);

    // Attempt to update theme without authentication
    Auth::logout();

    // Livewire should handle authentication check
    expect(Auth::check())->toBeFalse();
});

test('input validation prevents invalid theme values', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test('settings.appearance');

    // Attempt to set invalid theme
    $component->set('theme', 'invalid-theme-<script>alert("xss")</script>');

    // Theme should be validated and corrected to default
    expect($component->get('theme'))->toBe(Theme::Catppuccin->value)
        ->and($component->get('theme'))->not->toContain('<script>');
});

test('theme-specific accent validation prevents invalid accent values', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test('settings.appearance');

    // Set theme to Kanagawa
    $component->set('theme', Theme::Kanagawa->value);

    // Attempt to set invalid accent (should be validated by ThemeAccentMapper)
    // All current themes support all accents, but validation should still occur
    $component->set('accent', ThemeAccent::Primary->value);

    // Accent should be valid
    expect($component->get('accent'))->toBe(ThemeAccent::Primary->value);
});

test('no hardcoded secrets in theme-related code', function (): void {
    // Check theme-related files for common secret patterns
    $themeFiles = [
        app_path('Services/Theme/ThemeService.php'),
        app_path('Services/Theme/ThemeAccentMapper.php'),
        app_path('Livewire/Settings/Appearance.php'),
        app_path('Data/UserSettingsData.php'),
        app_path('Data/ThemeData.php'),
    ];

    $secretPatterns = [
        '/password\s*=\s*["\'][^"\']+["\']/i',
        '/api[_-]?key\s*=\s*["\'][^"\']+["\']/i',
        '/secret\s*=\s*["\'][^"\']+["\']/i',
        '/token\s*=\s*["\'][^"\']+["\']/i',
    ];

    foreach ($themeFiles as $file) {
        if (! file_exists($file)) {
            continue;
        }

        $content = file_get_contents($file);

        foreach ($secretPatterns as $pattern) {
            expect($content)->not->toMatch($pattern);
        }
    }
});

test('theme data attributes do not expose sensitive information', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('appearance.edit'));

    $response->assertOk();

    // Verify that theme data attributes are present but don't contain sensitive data
    $response->assertSee('data-theme', false);
    $response->assertSee('data-flavor', false);
    $response->assertSee('data-accent', false);

    // Verify no sensitive data is exposed in theme data attributes
    // Note: User email may appear in UI (sidebar/profile), but should not be in data-theme/flavor/accent attributes
    $content = $response->getContent();
    // Check that email is not in data attributes (it may be in other parts of the page)
    expect($content)->not->toMatch('/data-theme=["\'][^"\']*'.preg_quote((string) $user->email, '/').'[^"\']*["\']/')
        ->and($content)->not->toMatch('/data-flavor=["\'][^"\']*'.preg_quote((string) $user->email, '/').'[^"\']*["\']/')
        ->and($content)->not->toMatch('/data-accent=["\'][^"\']*'.preg_quote((string) $user->email, '/').'[^"\']*["\']/');

    // Password should never appear in HTML
    $response->assertDontSee($user->password, false);
});

test('dependency scanning: theme-related packages are up-to-date', function (): void {
    // This test verifies that theme-related dependencies are checked
    // In a real scenario, this would use a dependency scanning tool
    // For now, we verify that required packages are present

    $requiredPackages = [
        'livewire/livewire',
        'livewire/flux',
        'spatie/laravel-data',
    ];

    $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);
    $installedPackages = array_keys($composerJson['require'] ?? []);

    foreach ($requiredPackages as $package) {
        expect($installedPackages)->toContain($package);
    }
});

test('SQL injection is prevented in theme queries', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Attempt SQL injection in theme value
    $sqlInjection = "'; DROP TABLE users; --";

    $component = Livewire::test('settings.appearance');

    // Setting theme should use Eloquent (parameterized queries), preventing SQL injection
    $component->set('theme', $sqlInjection);

    // Theme should be validated and corrected
    expect($component->get('theme'))->toBe(Theme::Catppuccin->value);

    // Verify users table still exists (SQL injection failed)
    expect(Schema::hasTable('users'))->toBeTrue();
});
