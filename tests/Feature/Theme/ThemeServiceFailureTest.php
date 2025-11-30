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

test('ThemeAccentMapper service failure falls back to default theme', function (): void {
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

    // Create service with failing mapper
    $themeService = new ThemeService($failingMapper);

    // Should fallback to default theme when mapper fails
    $themeData = $themeService->resolveThemeData($user->settings);
    expect($themeData->theme)->toBe(Theme::Catppuccin) // Default theme
        ->and($themeData->flavor)->toBe(ThemeFlavor::Mocha) // Default flavor
        ->and($themeData->accent)->toBe(ThemeAccent::Primary); // Default accent
});

test('error is logged when ThemeAccentMapper service fails', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        ),
    ]);

    Log::shouldReceive('error')
        ->once()
        ->with('ThemeAccentMapper service failure', Mockery::on(fn (array $context): bool => isset($context['exception'])
            && isset($context['theme'])
            && isset($context['accent'])
            && $context['theme'] === Theme::Catppuccin->value
            && $context['accent'] === ThemeAccent::Primary->value));

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

    $themeService = new ThemeService($failingMapper);
    $themeService->resolveThemeData($user->settings);
});

test('graceful degradation when ThemeAccentMapper service fails (no user-facing errors)', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave,
            accent: ThemeAccent::Secondary,
        ),
    ]);

    $this->actingAs($user);

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

    // Replace the mapper in the service container for this test
    $this->app->instance(ThemeAccentMapperInterface::class, $failingMapper);
    // Also replace ThemeService to use the failing mapper
    $this->app->instance(ThemeService::class, new ThemeService($failingMapper));

    // Livewire component should still work, falling back to defaults
    // The component uses ThemeService which will handle the failure gracefully
    $component = Livewire::test('settings.appearance');

    // Component should mount successfully - ThemeService will fallback to defaults when mapper fails
    // The component's resolveThemeData() uses ThemeService which handles the failure
    $component->assertSet('theme', Theme::Catppuccin->value) // Default theme (fallback)
        ->assertSet('flavor', ThemeFlavor::Mocha->value) // Default flavor (fallback)
        ->assertSet('accent', ThemeAccent::Primary->value); // Default accent (fallback)

    // No user-facing errors should be displayed
    $component->assertHasNoErrors();
});
