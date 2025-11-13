<?php

declare(strict_types=1);

use App\Models\User;

test('guests are redirected to the login page', function (): void {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});

test('filament script attributes are applied from config overrides', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $originalConfig = config('filament.assets');

    config()->set('filament.assets.scripts', [
        'defer' => true,
        'async' => false,
        'attributes' => [
            'data-turbo-eval' => 'false',
            'data-qa-script' => 'true',
        ],
        'targets' => ['*'],
        'exclude' => [],
    ]);
    config()->set('filament.assets.load_alpine', true);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('data-turbo-eval=', escape: false);
    $response->assertSee('data-qa-script="true"', escape: false);
    $response->assertSee('<script', escape: false);
    $response->assertSee('defer', escape: false);

    config()->set('filament.assets', $originalConfig);
});

test('filament scripts can be excluded and alpine disabled through config', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $originalConfig = config('filament.assets');

    config()->set('filament.assets.scripts', [
        'defer' => false,
        'async' => false,
        'attributes' => [],
        'targets' => ['*'],
        'exclude' => ['filament/filament:app'],
    ]);
    config()->set('filament.assets.load_alpine', false);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('filament/filament/app.js');
    $response->assertDontSee('/components/', escape: false);

    config()->set('filament.assets', $originalConfig);
});
