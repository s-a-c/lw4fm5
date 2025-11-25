<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeFlavor;
use App\Http\Middleware\ApplyTheme;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;

test('middleware injects theme attributes into response', function (): void {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave
        ),
    ]);

    Route::get('/test-theme', fn (): Factory|View => view('components.layouts.app', ['slot' => 'content']))->middleware([ApplyTheme::class]);

    /** @var Tests\TestCase $this */
    /** @var TestResponse<Response> $response */
    $response = $this->actingAs($user)->get('/test-theme');

    $response->assertOk();
    $response->assertViewHas('themeValue', 'kanagawa');
});
