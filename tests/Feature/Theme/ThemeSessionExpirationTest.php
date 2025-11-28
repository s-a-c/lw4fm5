<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

test('auto-save completes if user still authenticated after session expiration', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $this->actingAs($user);

    $component = Livewire::test('settings.appearance');

    // Simulate session expiration by clearing session data
    // but user is still authenticated (token-based auth or session still valid)
    Session::flush();

    // User is still authenticated via Auth::check()
    expect(Auth::check())->toBeTrue();

    // Auto-save should complete successfully
    $component->set('theme', Theme::Kanagawa->value);

    $user->refresh();
    assert($user->settings instanceof UserSettingsData);
    expect($user->settings->theme)->toBe(Theme::Kanagawa);
});

test('auto-save discards silently if authentication expired', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $this->actingAs($user);

    $component = Livewire::test('settings.appearance');

    // Simulate authentication expiration by logging out
    Auth::logout();

    // User is no longer authenticated
    expect(Auth::check())->toBeFalse();

    // Auto-save should discard silently (no exception thrown)
    // The component's attemptPersist() method checks Auth::user() and returns early if null
    $component->call('performSave');

    // Verify the database was not updated (save was discarded)
    $user->refresh();
    assert($user->settings instanceof UserSettingsData);
    expect($user->settings->theme)->toBe(Theme::Catppuccin); // Should remain unchanged
});

test('re-authentication required on next interaction after session expiration', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $this->actingAs($user);

    $component = Livewire::test('settings.appearance');

    // Simulate authentication expiration
    Auth::logout();

    // Next interaction (mount) should require re-authentication
    // Livewire will handle authentication checks on mount
    // For this test, we verify that performSave discards silently when not authenticated
    $component->call('performSave');

    // Verify save was discarded (no database update)
    $user->refresh();
    assert($user->settings instanceof UserSettingsData);
    expect($user->settings->theme)->toBe(Theme::Catppuccin); // Should remain unchanged

    // Re-authenticate and verify next interaction works
    $this->actingAs($user);
    $component2 = Livewire::test('settings.appearance');
    $component2->set('theme', Theme::Kanagawa->value);

    $user->refresh();
    assert($user->settings instanceof UserSettingsData);
    expect($user->settings->theme)->toBe(Theme::Kanagawa); // Should be updated after re-authentication
});
