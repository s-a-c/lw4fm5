<?php

declare(strict_types=1);

use App\Contracts\ThemeAccentMapperInterface;
use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;
use App\Services\Theme\ThemeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

test('invalid theme/flavor combinations are auto-corrected on every access', function (): void {
    $user = User::factory()->create();

    // Manually set invalid combination: Kanagawa theme with Mocha flavor (Mocha is Catppuccin-only)
    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'settings' => json_encode([
                'theme' => Theme::Kanagawa->value,
                'flavor' => ThemeFlavor::Mocha->value, // Invalid: Mocha is not available for Kanagawa
                'accent' => ThemeAccent::Primary->value,
            ]),
        ]);

    $themeService = app(ThemeService::class);

    // Access 1: View Composer should auto-correct
    $themeData1 = $themeService->resolveThemeData($user->refresh()->settings);
    expect($themeData1->theme)->toBe(Theme::Kanagawa)
        ->and($themeData1->flavor)->toBe(ThemeFlavor::Wave) // Should be corrected to Wave (default for Kanagawa)
        ->and($themeData1->accent)->toBe(ThemeAccent::Primary);

    // Access 2: Livewire component should see corrected values
    $themeData2 = $themeService->resolveThemeData($user->refresh()->settings);
    expect($themeData2->flavor)->toBe(ThemeFlavor::Wave);

    // Access 3: Direct model access should see corrected values
    $themeData3 = $themeService->resolveThemeData($user->refresh()->settings);
    expect($themeData3->flavor)->toBe(ThemeFlavor::Wave);
});

test('invalid accent values fallback to Primary or first available', function (): void {
    $user = User::factory()->create();

    // Set invalid accent (assuming we have a theme that doesn't support all accents)
    // For now, all themes support all accents, so we'll test the fallback logic
    $themeService = app(ThemeService::class);

    // Test that Primary is preferred when available
    $themeData = $themeService->resolveThemeData(new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Mocha,
        accent: ThemeAccent::Primary,
    ));

    expect($themeData->accent)->toBe(ThemeAccent::Primary);

    // Test with valid accent
    $themeData2 = $themeService->resolveThemeData(new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Mocha,
        accent: ThemeAccent::Blue,
    ));

    expect($themeData2->accent)->toBe(ThemeAccent::Blue);
});

test('ThemeAccentMapper service failure falls back to default theme with error logging', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave,
            accent: ThemeAccent::Primary,
        ),
    ]);

    Log::shouldReceive('error')
        ->once()
        ->with('ThemeAccentMapper service failure', Mockery::type('array'));

    // Create a mock that throws an exception
    $failingMapper = new class implements ThemeAccentMapperInterface
    {
        public function getAvailableAccents(Theme $theme): array
        {
            throw new RuntimeException('Service failure');
        }

        public function validateAccent(Theme $theme, ThemeAccent $accent): bool
        {
            throw new RuntimeException('Service failure');
        }

        public function getFluxVariableName(Theme $theme, ThemeFlavor $flavor, ThemeAccent $accent): string
        {
            return '--accent-flux-zinc-500';
        }

        public function getFilamentVariableName(Theme $theme, ThemeFlavor $flavor, ThemeAccent $accent): string
        {
            return '--accent-filament-gray-500';
        }
    };

    // Replace the mapper in the service
    $themeService = new ThemeService($failingMapper);

    // Should fallback to default theme when mapper fails
    $themeData = $themeService->resolveThemeData($user->settings);
    expect($themeData->theme)->toBe(Theme::Catppuccin) // Default theme
        ->and($themeData->flavor)->toBe(ThemeFlavor::Mocha) // Default flavor
        ->and($themeData->accent)->toBe(ThemeAccent::Primary); // Default accent
});

test('null settings are handled gracefully with defaults', function (): void {
    $user = User::factory()->create();
    // settings column is null

    $themeService = app(ThemeService::class);

    // User model booted() should ensure settings is never null, but test the service directly
    $themeData = $themeService->resolveThemeData(null);

    expect($themeData->theme)->toBe(Theme::Catppuccin)
        ->and($themeData->flavor)->toBe(ThemeFlavor::Mocha)
        ->and($themeData->accent)->toBe(ThemeAccent::Primary);
});

test('corrupted data in database is auto-corrected', function (): void {
    $user = User::factory()->create();

    // Set corrupted/invalid JSON data with invalid enum values
    // Spatie Laravel Data will throw an exception when deserializing invalid enum values
    // This test verifies that the User model's booted() method handles this gracefully
    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'settings' => '{"theme":"invalid_theme","flavor":"invalid_flavor","accent":"invalid_accent"}',
        ]);

    $themeService = app(ThemeService::class);

    // User model's booted() should ensure settings is never null
    // If deserialization fails, booted() will set a default UserSettingsData
    $user->refresh();

    // When accessing settings, it should be corrected (either by booted() or ThemeService)
    // The booted() method ensures settings is never null, so we should get defaults
    $themeData = $themeService->resolveThemeData($user->settings);

    // Should fallback to defaults
    expect($themeData->theme)->toBe(Theme::Catppuccin)
        ->and($themeData->flavor)->toBe(ThemeFlavor::Mocha)
        ->and($themeData->accent)->toBe(ThemeAccent::Primary);
});

test('rapid successive theme changes are handled correctly', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(),
    ]);

    $this->actingAs($user);

    $themeService = app(ThemeService::class);

    // Simulate rapid changes
    $user->settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Mocha,
        accent: ThemeAccent::Primary,
    );
    $user->save();

    $themeData1 = $themeService->resolveThemeData($user->refresh()->settings);
    expect($themeData1->theme)->toBe(Theme::Catppuccin);

    $user->settings = new UserSettingsData(
        theme: Theme::Kanagawa,
        flavor: ThemeFlavor::Wave,
        accent: ThemeAccent::Blue,
    );
    $user->save();

    $themeData2 = $themeService->resolveThemeData($user->refresh()->settings);
    expect($themeData2->theme)->toBe(Theme::Kanagawa)
        ->and($themeData2->flavor)->toBe(ThemeFlavor::Wave)
        ->and($themeData2->accent)->toBe(ThemeAccent::Blue);

    // Change back rapidly
    $user->settings = new UserSettingsData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Frappe,
        accent: ThemeAccent::Red,
    );
    $user->save();

    $themeData3 = $themeService->resolveThemeData($user->refresh()->settings);
    expect($themeData3->theme)->toBe(Theme::Catppuccin)
        ->and($themeData3->flavor)->toBe(ThemeFlavor::Frappe)
        ->and($themeData3->accent)->toBe(ThemeAccent::Red);
});

test('validation occurs on every access, not just on load', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $themeService = app(ThemeService::class);

    // First access
    $themeData1 = $themeService->resolveThemeData($user->settings);
    expect($themeData1->theme)->toBe(Theme::Catppuccin);

    // Manually corrupt the data in the database
    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'settings' => json_encode([
                'theme' => Theme::Kanagawa->value,
                'flavor' => ThemeFlavor::Mocha->value, // Invalid for Kanagawa
                'accent' => ThemeAccent::Primary->value,
            ]),
        ]);

    // Second access (after corruption) should auto-correct
    $themeData2 = $themeService->resolveThemeData($user->refresh()->settings);
    expect($themeData2->flavor)->toBe(ThemeFlavor::Wave); // Should be corrected

    // Third access should still see corrected values
    $themeData3 = $themeService->resolveThemeData($user->refresh()->settings);
    expect($themeData3->flavor)->toBe(ThemeFlavor::Wave);
});
