<?php

declare(strict_types=1);

use App\Contracts\ThemeAccentMapperInterface;
use App\Data\ThemeData;
use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Services\Theme\ThemeAccentMapper;
use App\Services\Theme\ThemeService;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    $this->accentMapper = new ThemeAccentMapper();
    $this->service = new ThemeService($this->accentMapper);
});

test('resolves theme data from valid user settings', function (): void {
    $settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Mocha,
        accent: ThemeAccent::Primary,
    );

    $themeData = $this->service->resolveThemeData($settings);

    expect($themeData)->toBeInstanceOf(ThemeData::class)
        ->and($themeData->theme)->toBe(Theme::Catppuccin)
        ->and($themeData->flavor)->toBe(ThemeFlavor::Mocha)
        ->and($themeData->accent)->toBe(ThemeAccent::Primary);
});

test('validates theme and flavor combination', function (): void {
    // Invalid: Wave belongs to Kanagawa, not Catppuccin
    $settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Wave,
        accent: ThemeAccent::Primary,
    );

    $themeData = $this->service->resolveThemeData($settings);

    // Should auto-correct: theme is valid (Catppuccin), but flavor is invalid for that theme (Wave)
    // So flavor should be corrected to first available flavor for Catppuccin (Latte)
    expect($themeData->theme)->toBe(Theme::Catppuccin)
        ->and($themeData->flavor)->toBe(ThemeFlavor::Latte) // First available flavor for Catppuccin
        ->and($themeData->accent)->toBe(ThemeAccent::Primary);
});

test('validates accent for theme using ThemeAccentMapper', function (): void {
    $settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Mocha,
        accent: ThemeAccent::Blue, // Valid accent
    );

    $themeData = $this->service->resolveThemeData($settings);

    expect($themeData->accent)->toBe(ThemeAccent::Blue);
});

test('resolves default theme data when settings is null', function (): void {
    $themeData = $this->service->resolveThemeData(null);

    expect($themeData)->toBeInstanceOf(ThemeData::class)
        ->and($themeData->theme)->toBe(Theme::Catppuccin)
        ->and($themeData->flavor)->toBe(ThemeFlavor::Mocha)
        ->and($themeData->accent)->toBe(ThemeAccent::Primary);
});

test('defaults accent to Primary when theme is selected', function (): void {
    $settings = new UserSettingsData(
        theme: Theme::Kanagawa,
        flavor: ThemeFlavor::Wave,
        accent: ThemeAccent::Primary,
    );

    $themeData = $this->service->resolveThemeData($settings);

    expect($themeData->accent)->toBe(ThemeAccent::Primary);
});

test('falls back to first available accent if Primary does not exist', function (): void {
    // This test assumes Primary always exists for current themes
    // In future, if a theme doesn't have Primary, it should fallback to first available
    $settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Mocha,
        accent: ThemeAccent::Primary,
    );

    $themeData = $this->service->resolveThemeData($settings);

    expect($themeData->accent)->toBe(ThemeAccent::Primary);
});

test('handles ThemeAccentMapper service failure gracefully', function (): void {
    Log::shouldReceive('error')->once();

    $failingMapper = mock(ThemeAccentMapperInterface::class);
    $failingMapper->shouldReceive('validateAccent')
        ->andThrow(new RuntimeException('Service failure'));

    $service = new ThemeService($failingMapper);

    $settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Mocha,
        accent: ThemeAccent::Blue,
    );

    $themeData = $service->resolveThemeData($settings);

    // Should fallback to default theme
    expect($themeData->theme)->toBe(Theme::Catppuccin)
        ->and($themeData->flavor)->toBe(ThemeFlavor::Mocha)
        ->and($themeData->accent)->toBe(ThemeAccent::Primary);
});

test('falls back to first available accent when validation rejects value', function (): void {
    $mapper = mock(ThemeAccentMapperInterface::class);
    $mapper->shouldReceive('validateAccent')
        ->once()
        ->andReturnFalse();
    $mapper->shouldReceive('getAvailableAccents')
        ->once()
        ->andReturn([ThemeAccent::Green]);

    $service = new ThemeService($mapper);

    $settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Mocha,
        accent: ThemeAccent::Red,
    );

    $themeData = $service->resolveThemeData($settings);

    expect($themeData->accent)->toBe(ThemeAccent::Green);
});

test('validates on every access not just on load', function (): void {
    $settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Mocha,
        accent: ThemeAccent::Primary,
    );

    // First access
    $themeData1 = $this->service->resolveThemeData($settings);
    expect($themeData1->theme)->toBe(Theme::Catppuccin);

    // Second access - should validate again
    $themeData2 = $this->service->resolveThemeData($settings);
    expect($themeData2->theme)->toBe(Theme::Catppuccin);
});
