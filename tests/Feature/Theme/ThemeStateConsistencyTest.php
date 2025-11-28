<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;
use App\Services\Theme\ThemeService;
use Livewire\Livewire;

test('theme state is consistent between User model and Livewire component', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $this->actingAs($user);

    $component = Livewire::test('settings.appearance');

    // Verify initial state matches
    expect($component->get('theme'))->toBe(Theme::Catppuccin->value)
        ->and($component->get('flavor'))->toBe(ThemeFlavor::Mocha->value)
        ->and($component->get('accent'))->toBe(ThemeAccent::Primary->value);

    // Update via Livewire component
    $component->set('theme', Theme::Kanagawa->value)
        ->set('flavor', ThemeFlavor::Wave->value)
        ->set('accent', ThemeAccent::Blue->value);

    // Verify User model is updated
    $settings = $user->refresh()->settings;
    assert($settings instanceof UserSettingsData);
    expect($settings->theme)->toBe(Theme::Kanagawa)
        ->and($settings->flavor)->toBe(ThemeFlavor::Wave)
        ->and($settings->accent)->toBe(ThemeAccent::Blue);
});

test('theme state is consistent between User model and View Composer', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $this->actingAs($user);

    $themeService = app(ThemeService::class);

    // Verify View Composer sees correct state
    $themeData = $themeService->resolveThemeData($user->settings);
    expect($themeData->theme)->toBe(Theme::Catppuccin)
        ->and($themeData->flavor)->toBe(ThemeFlavor::Mocha)
        ->and($themeData->accent)->toBe(ThemeAccent::Primary);

    // Update User model directly
    $user->settings = new UserSettingsData(
        theme: Theme::Kanagawa,
        flavor: ThemeFlavor::Wave,
        accent: ThemeAccent::Blue,
    );
    $user->save();

    // Verify View Composer sees updated state
    $themeData = $themeService->resolveThemeData($user->refresh()->settings);
    expect($themeData->theme)->toBe(Theme::Kanagawa)
        ->and($themeData->flavor)->toBe(ThemeFlavor::Wave)
        ->and($themeData->accent)->toBe(ThemeAccent::Blue);
});

test('theme state synchronization is immediate when updated via Livewire component', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(),
    ]);

    $this->actingAs($user);

    $component = Livewire::test('settings.appearance');

    // Update theme via Livewire
    $component->set('theme', Theme::Kanagawa->value);

    // Immediately check User model (should be updated)
    $settings = $user->refresh()->settings;
    assert($settings instanceof UserSettingsData);
    expect($settings->theme)->toBe(Theme::Kanagawa);

    // View Composer should also see updated state
    $themeService = app(ThemeService::class);
    $themeData = $themeService->resolveThemeData($user->settings);
    expect($themeData->theme)->toBe(Theme::Kanagawa);
});

test('theme state is consistent when updated via multiple paths', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(),
    ]);

    $this->actingAs($user);

    $themeService = app(ThemeService::class);

    // Path 1: Update via Livewire component
    $component = Livewire::test('settings.appearance');
    $component->set('theme', Theme::Kanagawa->value);

    $settings = $user->refresh()->settings;
    assert($settings instanceof UserSettingsData);
    expect($settings->theme)->toBe(Theme::Kanagawa);

    // Path 2: Update via direct model update
    $user->settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Frappe,
        accent: ThemeAccent::Red,
    );
    $user->save();

    // Verify all paths see consistent state
    $settings = $user->refresh()->settings;
    assert($settings instanceof UserSettingsData);
    expect($settings->theme)->toBe(Theme::Catppuccin)
        ->and($settings->flavor)->toBe(ThemeFlavor::Frappe)
        ->and($settings->accent)->toBe(ThemeAccent::Red);

    // View Composer should see the same state
    $themeData = $themeService->resolveThemeData($user->settings);
    expect($themeData->theme)->toBe(Theme::Catppuccin)
        ->and($themeData->flavor)->toBe(ThemeFlavor::Frappe)
        ->and($themeData->accent)->toBe(ThemeAccent::Red);

    // Livewire component should see updated state on next render
    $component = Livewire::test('settings.appearance');
    expect($component->get('theme'))->toBe(Theme::Catppuccin->value)
        ->and($component->get('flavor'))->toBe(ThemeFlavor::Frappe->value)
        ->and($component->get('accent'))->toBe(ThemeAccent::Red->value);
});
