<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;

test('login page has theme data attributes in HTML', function (): void {
    $response = $this->get(route('login'));

    $response->assertOk();

    // Verify theme data attributes are present in the HTML (FR-041)
    $response->assertSee('data-theme="catppuccin"', false)
        ->assertSee('data-flavor="mocha"', false)
        ->assertSee('data-accent="primary"', false);
});

test('register page has theme data attributes in HTML', function (): void {
    $response = $this->get(route('register'));

    $response->assertOk();

    // Verify theme data attributes are present (FR-041)
    $response->assertSee('data-theme="catppuccin"', false)
        ->assertSee('data-flavor="mocha"', false)
        ->assertSee('data-accent="primary"', false);
});

test('password reset page has theme data attributes in HTML', function (): void {
    $response = $this->get(route('password.request'));

    $response->assertOk();

    // Verify theme data attributes are present (FR-041)
    $response->assertSee('data-theme="catppuccin"', false)
        ->assertSee('data-flavor="mocha"', false)
        ->assertSee('data-accent="primary"', false);
});

test('auth pages apply themes correctly in components', function (): void {
    // Test login page
    $loginResponse = $this->get(route('login'));
    $loginResponse->assertOk();

    // Verify dark class is applied based on theme flavor
    // Mocha is dark, so dark class should be present
    $loginResponse->assertSee('class="dark"', false);

    // Verify theme attributes are in HTML element
    $loginResponse->assertSee('data-theme="catppuccin"', false);
});

test('authenticated user theme preferences apply to auth pages after login', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave,
            accent: ThemeAccent::Blue,
        ),
    ]);

    // Login and verify theme is applied
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // After login, check that theme preferences are available
    $this->actingAs($user);

    // Access any authenticated page to verify theme is applied
    $dashboardResponse = $this->get(route('dashboard'));
    $dashboardResponse->assertOk();

    // Theme should be applied based on user preferences
    $dashboardResponse->assertSee('data-theme="kanagawa"', false)
        ->assertSee('data-flavor="wave"', false)
        ->assertSee('data-accent="blue"', false);
});

test('auth pages remain accessible when themed', function (): void {
    // Test login page accessibility (FR-065)
    $response = $this->get(route('login'));

    $response->assertOk();

    // Verify form labels are readable
    $response->assertSee('Email address', false)
        ->assertSee('Password', false);

    // Verify theme attributes don't break accessibility
    $response->assertSee('data-theme', false)
        ->assertSee('data-flavor', false)
        ->assertSee('data-accent', false);
});

test('graceful degradation when CSS fails: theme still readable', function (): void {
    // Test that page is still readable without CSS (FR-070)
    $response = $this->get(route('login'));

    $response->assertOk();

    // Verify essential content is present
    $response->assertSee('Log in to your account', false)
        ->assertSee('Email address', false)
        ->assertSee('Password', false);

    // Theme data attributes should still be present (for JavaScript fallback)
    $response->assertSee('data-theme', false);
});

test('graceful degradation when JavaScript fails: theme still readable', function (): void {
    // Test that page is still readable without JavaScript (FR-070)
    $response = $this->get(route('login'));

    $response->assertOk();

    // Verify essential content is present
    $response->assertSee('Log in to your account', false)
        ->assertSee('Email address', false)
        ->assertSee('Password', false);

    // HTML data attributes should still be present (server-side injection)
    $response->assertSee('data-theme="catppuccin"', false)
        ->assertSee('data-flavor="mocha"', false)
        ->assertSee('data-accent="primary"', false);
});

test('session fixation prevention: session regenerates on authentication', function (): void {
    // Get initial session ID
    $this->get(route('login'));
    $initialSessionId = session()->getId();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    // Login
    $response = $this->post(route('login.store'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    // Session should be regenerated after authentication (FR-074)
    $newSessionId = session()->getId();
    expect($newSessionId)->not->toBe($initialSessionId);
});
