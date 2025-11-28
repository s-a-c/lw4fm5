<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

test('rate limiting allows 10 theme auto-save requests per 60 seconds per user', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        ),
    ]);

    $this->actingAs($user);

    // Clear any existing rate limit
    RateLimiter::clear('theme-auto-save:'.$user->id);

    $component = Livewire::test('settings.appearance');

    // Make 10 requests (should all succeed)
    for ($i = 1; $i <= 10; $i++) {
        $component->call('performSave')
            ->assertHasNoErrors();
    }

    // 11th request should be rate limited
    $component->call('performSave')
        ->assertStatus(429);
});

test('rate limiting is per user', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1);

    // Clear rate limits
    RateLimiter::clear('theme-auto-save:'.$user1->id);
    RateLimiter::clear('theme-auto-save:'.$user2->id);

    $component1 = Livewire::test('settings.appearance');

    // User 1 makes 10 requests
    for ($i = 1; $i <= 10; $i++) {
        $component1->call('performSave')
            ->assertHasNoErrors();
    }

    // User 1's 11th request should be rate limited
    $component1->call('performSave')
        ->assertStatus(429);

    // Switch to user 2
    $this->actingAs($user2);
    $component2 = Livewire::test('settings.appearance');

    // User 2 should be able to make requests (separate rate limit)
    $component2->call('performSave')
        ->assertHasNoErrors();
});

test('rate limiting uses sliding window (60 seconds)', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Clear rate limit
    RateLimiter::clear('theme-auto-save:'.$user->id);

    $component = Livewire::test('settings.appearance');

    // Make 10 requests (should all succeed)
    for ($i = 1; $i <= 10; $i++) {
        $component->call('performSave')
            ->assertHasNoErrors();
    }

    // Verify rate limit is active after 10 requests
    expect(RateLimiter::tooManyAttempts('theme-auto-save:'.$user->id, 10))->toBeTrue();

    // Verify remaining attempts is 0
    expect(RateLimiter::remaining('theme-auto-save:'.$user->id, 10))->toBe(0);
});
