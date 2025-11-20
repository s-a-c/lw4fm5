<?php

declare(strict_types=1);

use App\Enums\Theme;
use App\Models\User;
use Livewire\Livewire;

test('user can switch themes', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('settings.appearance')
        ->set('theme', Theme::Kanagawa->value)
        ->assertSet('theme', Theme::Kanagawa->value);

    $user->refresh();
    expect($user->settings->theme)->toBe(Theme::Kanagawa);
});
