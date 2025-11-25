<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
            'two_factor_confirmed_at',
            'settings',
        ]);
});

test('initials return the first letters of the first two words', function (): void {
    $user = User::factory()->create(['name' => 'Ada Augusta Lovelace']);

    expect($user->initials())->toBe('AA');
});

test('settings default to data object when missing', function (): void {
    /** @var User $user */
    $user = User::factory()->create(['settings' => null]);

    $fresh = User::query()->findOrFail($user->id);

    expect($fresh->settings)->toBeInstanceOf(UserSettingsData::class);
});

test('users can access any filament panel', function (): void {
    $user = User::factory()->create();
    $panel = Panel::make()->id('admin')->path('admin');

    expect($user->canAccessPanel($panel))->toBeTrue();
});
