<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

test('guests are redirected to the login page', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->get(route('dashboard'));
    expect($response)->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();
    $this->actingAs($user);

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->get(route('dashboard'));
    expect($response)->assertOk();
});

test('filament script attributes are applied from config overrides', function (): void {
    /** @phpstan-var Tests\TestCase $this */
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

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->get(route('dashboard'));

    expect($response)
        ->assertOk()
        ->assertSee('data-turbo-eval=', escape: false)
        ->assertSee('data-qa-script="true"', escape: false)
        ->assertSee('<script', escape: false)
        ->assertSee('defer', escape: false);

    config()->set('filament.assets', $originalConfig);
});

test('filament scripts can be excluded and alpine disabled through config', function (): void {
    /** @phpstan-var Tests\TestCase $this */
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

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->get(route('dashboard'));

    expect($response)
        ->assertOk()
        ->assertDontSee('filament/filament/app.js')
        ->assertDontSee('/components/', escape: false);

    config()->set('filament.assets', $originalConfig);
});
