<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Fortify\Features;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;

test('login screen can be rendered', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->withoutTwoFactor()->create();

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    assertAuthenticated();
});

test('users can not authenticate with invalid password', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    if (! Features::canManageTwoFactorAuthentication()) {
        skip('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->create();

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    assertGuest();
});

test('users can logout', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    assertGuest();
});
