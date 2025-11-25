<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;

test('reset password link screen can be rendered', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @var TestResponse<Response> $response */
    $response = $this->get(route('password.request'));

    $response->assertOk();
});

test('reset password link can be requested', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification): true {
        /** @phpstan-var Tests\TestCase $this */
        /** @var TestResponse<Response> $response */
        $response = $this->get(route('password.reset', $notification->token));

        $response->assertOk();

        return true;
    });
});

test('password can be reset with valid token', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user): true {
        /** @phpstan-var Tests\TestCase $this */
        /** @var TestResponse<Response> $response */
        $response = $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'NewPassword123!@#',
            'password_confirmation' => 'NewPassword123!@#',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('login', absolute: false));

        return true;
    });
});
