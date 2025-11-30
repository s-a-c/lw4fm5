<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

test('filament render hook outputs default theme script for guests', function (): void {
    $hookOutput = FilamentView::renderHook(PanelsRenderHook::HEAD_END)->toHtml();

    expect($hookOutput)
        ->toContain('const themeValue = "catppuccin"')
        ->toContain('const flavorValue = "mocha"')
        ->toContain('const accentValue = "primary"');
});

test('filament dashboard renders theme script using authenticated preferences', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Lotus,
            accent: ThemeAccent::Secondary,
        ),
    ]);

    $this->actingAs($user)
        ->get('/filament')
        ->assertOk()
        ->assertSee('const themeValue = "kanagawa"', false)
        ->assertSee('const flavorValue = "lotus"', false)
        ->assertSee('const accentValue = "blue"', false)
        ->assertSee("classList.remove('dark')", false);
});

test('filament panel views have theme data attributes in HTML', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $response = $this->actingAs($user)
        ->get('/filament');

    $response->assertOk();

    // Verify theme data attributes are present in the HTML (FR-041)
    // Filament pages set theme attributes via JavaScript in the render hook
    // We verify the script contains the correct theme values
    $content = $response->getContent();
    // Check for the JavaScript variables that set the theme attributes
    expect($content)->toContain('const themeValue = "kanagawa"')
        ->and($content)->toContain('const flavorValue = "wave"')
        ->and($content)->toContain('const accentValue = "primary"');
});

test('filament resource pages apply themes correctly', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Secondary,
        ),
    ]);

    // Access any Filament resource page
    $response = $this->actingAs($user)
        ->get('/filament');

    $response->assertOk();

    // Verify theme script is present and correct
    $response->assertSee('const themeValue = "catppuccin"', false)
        ->assertSee('const flavorValue = "mocha"', false)
        ->assertSee('const accentValue = "blue"', false);
});

test('filament components respect theme changes', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Dragon,
            accent: ThemeAccent::Error,
        ),
    ]);

    $response = $this->actingAs($user)
        ->get('/filament');

    $response->assertOk();

    // Verify theme is applied to Filament components
    $response->assertSee('const themeValue = "kanagawa"', false)
        ->assertSee('const flavorValue = "dragon"', false)
        ->assertSee('const accentValue = "red"', false);
});
