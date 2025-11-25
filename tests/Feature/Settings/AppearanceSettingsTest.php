<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Models\User;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

test('user can switch themes', function (): void {
    $user = User::factory()->create();

    /** @var Testable $component */
    $component = Livewire::actingAs($user)
        ->test('settings.appearance');
    $component->set('theme', Theme::Kanagawa->value);
    $component->assertSet('theme', Theme::Kanagawa->value);

    $user->refresh();
    $settings = $user->settings;
    expect($settings)->not->toBeNull();
    /** @var UserSettingsData $settings */
    expect($settings->theme)->toBe(Theme::Kanagawa);
});
