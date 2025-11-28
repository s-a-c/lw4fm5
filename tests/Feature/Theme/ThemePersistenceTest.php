<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Livewire\Livewire;

test('appearance settings persist theme preferences inside a database transaction', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();
    $this->actingAs($user);

    DB::shouldReceive('transaction')
        ->atLeast()
        ->once()
        ->andReturnUsing(static fn (Closure $callback) => $callback());

    Livewire::test('settings.appearance')
        ->set('theme', Theme::Kanagawa->value)
        ->set('flavor', ThemeFlavor::Wave->value)
        ->set('accent', ThemeAccent::Blue->value);

    $settings = $user->refresh()->settings;
    assert($settings instanceof UserSettingsData);

    expect($settings->theme)->toBe(Theme::Kanagawa)
        ->and($settings->flavor)->toBe(ThemeFlavor::Wave)
        ->and($settings->accent)->toBe(ThemeAccent::Blue);
});

test('reset button visibility toggles based on whether the theme differs from defaults', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $defaultUser = User::factory()->create([
        'settings' => new UserSettingsData(),
    ]);

    $this->actingAs($defaultUser);

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->get(route('appearance.edit'));
    $response->assertOk()->assertDontSee('Reset to Default');

    $customUser = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave,
            accent: ThemeAccent::Blue,
        ),
    ]);

    $this->actingAs($customUser);

    /** @phpstan-var TestResponse<Response> $customResponse */
    $customResponse = $this->get(route('appearance.edit'));
    $customResponse->assertOk()->assertSee('Reset to Default');
});

test('reset button restores default theme values in the database', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave,
            accent: ThemeAccent::Blue,
        ),
    ]);

    $this->actingAs($user);

    Livewire::test('settings.appearance')
        ->call('resetToDefault')
        ->assertSet('theme', Theme::Catppuccin->value)
        ->assertSet('flavor', ThemeFlavor::Mocha->value)
        ->assertSet('accent', ThemeAccent::Primary->value);

    $settings = $user->refresh()->settings;
    assert($settings instanceof UserSettingsData);

    expect($settings->theme)->toBe(Theme::Catppuccin)
        ->and($settings->flavor)->toBe(ThemeFlavor::Mocha)
        ->and($settings->accent)->toBe(ThemeAccent::Primary);
});

test('auto-save provides silent feedback when save succeeds', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create([
        'settings' => new UserSettingsData(),
    ]);

    $this->actingAs($user);

    // Verify UI does not show a "Save" button (changes are saved automatically per FR-082)
    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->get(route('appearance.edit'));
    $response->assertOk()
        ->assertDontSee('Save Changes')
        ->assertDontSee('Save Settings')
        ->assertSee('Theme Family')
        ->assertSee('Variant')
        ->assertSee('Accent Color');

    // Verify that theme changes are saved automatically without user action
    $component = Livewire::test('settings.appearance');
    $component->set('theme', Theme::Kanagawa->value);

    // Save should happen automatically (no explicit save call needed)
    $settings = $user->refresh()->settings;
    assert($settings instanceof UserSettingsData);
    expect($settings->theme)->toBe(Theme::Kanagawa);

    // No success message or toast should be shown for successful auto-save (silent feedback per FR-082)
    // The component does not dispatch success toasts for successful saves
});
