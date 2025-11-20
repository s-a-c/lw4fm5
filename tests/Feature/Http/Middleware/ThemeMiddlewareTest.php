<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeFlavor;
use App\Http\Middleware\ApplyTheme;
use App\Models\User;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\actingAs;

test('middleware injects theme attributes into response', function () {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave
        ),
    ]);

    Route::get('/test-theme', function () {
        return view('components.layouts.app', ['slot' => 'content']);
    })->middleware([ApplyTheme::class]);

    $response = $this->actingAs($user)->get('/test-theme');

    expect($response)
        ->assertOk()
        ->assertViewHas('themeValue', 'kanagawa');
});
