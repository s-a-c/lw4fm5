<?php

declare(strict_types=1);

use App\Contracts\ThemeAccentMapperInterface;
use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;
use App\Services\Theme\ThemeService;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;

test('theme-specific accent validation works correctly', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $mapper = app(ThemeAccentMapperInterface::class);

    // Verify accent validation for Catppuccin theme
    expect($mapper->validateAccent(Theme::Catppuccin, ThemeAccent::Primary))->toBeTrue()
        ->and($mapper->validateAccent(Theme::Catppuccin, ThemeAccent::Blue))->toBeTrue()
        ->and($mapper->validateAccent(Theme::Catppuccin, ThemeAccent::Red))->toBeTrue()
        ->and($mapper->validateAccent(Theme::Catppuccin, ThemeAccent::Green))->toBeTrue();

    // Verify accent validation for Kanagawa theme
    expect($mapper->validateAccent(Theme::Kanagawa, ThemeAccent::Primary))->toBeTrue()
        ->and($mapper->validateAccent(Theme::Kanagawa, ThemeAccent::Blue))->toBeTrue()
        ->and($mapper->validateAccent(Theme::Kanagawa, ThemeAccent::Red))->toBeTrue()
        ->and($mapper->validateAccent(Theme::Kanagawa, ThemeAccent::Green))->toBeTrue();
});

test('available accents per theme queries return correct results', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $mapper = app(ThemeAccentMapperInterface::class);

    // Get available accents for Catppuccin
    $catppuccinAccents = $mapper->getAvailableAccents(Theme::Catppuccin);
    expect($catppuccinAccents)->toBeArray()
        ->toContain(ThemeAccent::Primary)
        ->toContain(ThemeAccent::Blue)
        ->toContain(ThemeAccent::Red)
        ->toContain(ThemeAccent::Green)
        ->and(count($catppuccinAccents))->toBe(4);

    // Get available accents for Kanagawa
    $kanagawaAccents = $mapper->getAvailableAccents(Theme::Kanagawa);
    expect($kanagawaAccents)->toBeArray()
        ->toContain(ThemeAccent::Primary)
        ->toContain(ThemeAccent::Blue)
        ->toContain(ThemeAccent::Red)
        ->toContain(ThemeAccent::Green)
        ->and(count($kanagawaAccents))->toBe(4);
});

test('CSS variable name generation works correctly', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $mapper = app(ThemeAccentMapperInterface::class);

    // Test Flux CSS variable name generation
    $fluxVariable = $mapper->getFluxVariableName(
        Theme::Catppuccin,
        ThemeFlavor::Mocha,
        ThemeAccent::Primary
    );
    expect($fluxVariable)->toBe('--accent-flux-zinc-500');

    // Test Filament CSS variable name generation
    $filamentVariable = $mapper->getFilamentVariableName(
        Theme::Kanagawa,
        ThemeFlavor::Wave,
        ThemeAccent::Blue
    );
    expect($filamentVariable)->toBe('--accent-filament-gray-500');
});

test('service failure handling falls back to default theme with error logging', function (): void {
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

    // Replace the mapper in the service container
    $this->app->instance(ThemeAccentMapperInterface::class, $failingMapper);

    // Clear the service from container to force re-instantiation with new mapper
    $this->app->forgetInstance(ThemeService::class);

    // ThemeService should fallback to default theme when mapper fails
    $themeService = app(ThemeService::class);
    $themeData = $themeService->resolveThemeData($user->settings);

    // When mapper throws exception, service falls back to defaults (see ThemeService line 104-116)
    expect($themeData->theme)->toBe(Theme::Catppuccin) // Default theme
        ->and($themeData->flavor)->toBe(ThemeFlavor::Mocha) // Default flavor
        ->and($themeData->accent)->toBe(ThemeAccent::Primary); // Default accent
});

test('ThemeAccentMapper is used in Livewire component for accent validation', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test('settings.appearance');

    // Change theme to Kanagawa
    $component->set('theme', Theme::Kanagawa->value);

    // Available accents should be updated based on ThemeAccentMapper
    $availableAccents = $component->get('availableAccents');
    expect($availableAccents)->toBeArray()
        ->and(count($availableAccents))->toBeGreaterThan(0);

    // All available accents should be valid for the theme
    $mapper = app(ThemeAccentMapperInterface::class);
    foreach ($availableAccents as $accent) {
        expect($mapper->validateAccent(Theme::Kanagawa, $accent))->toBeTrue();
    }
});
