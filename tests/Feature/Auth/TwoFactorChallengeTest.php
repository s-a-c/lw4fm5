<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Fortify\Features;

test('two factor challenge redirects to login when not authenticated', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    if (! Features::canManageTwoFactorAuthentication()) {
        skip('Two-factor authentication is not enabled.');
    }

    /** @var TestResponse<Response> $response */
    $response = $this->get(route('two-factor.login'));

    $response->assertRedirect(route('login'));
});

test('two factor challenge can be rendered', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    if (! Features::canManageTwoFactorAuthentication()) {
        skip('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->create();

    /** @var TestResponse<Response> $response */
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);
    $response->assertRedirect(route('two-factor.login'));
});
