<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;

test('view composer injects themeData with all three attributes for authenticated user', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave,
            accent: ThemeAccent::Secondary,
        ),
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('data-theme="kanagawa"', false);
    $response->assertSee('data-flavor="wave"', false);
    $response->assertSee('data-accent="blue"', false);
});

test('view composer injects default themeData for unauthenticated user', function (): void {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('data-theme="catppuccin"', false);
    $response->assertSee('data-flavor="mocha"', false);
    $response->assertSee('data-accent="primary"', false);
});

test('view composer injects themeData for user with null settings', function (): void {
    $user = User::factory()->create([
        'settings' => null,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('data-theme="catppuccin"', false);
    $response->assertSee('data-flavor="mocha"', false);
    $response->assertSee('data-accent="primary"', false);
});

test('view composer applies dark class ThemeGlobalApplicationTest dark flavors', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('class="dark"', false);
});

test('view composer removes dark class ThemeGlobalApplicationTest light flavors', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Latte,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertDontSee('class="dark"', false);
});
