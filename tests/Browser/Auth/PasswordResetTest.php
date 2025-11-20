<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

test('reset password link screen can be rendered', function (): void {
    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('password.request'))
            ->assertSee('Forgot password')
            ->assertSee('Enter your email to receive a password reset link')
            ->assertNoConsoleLogs()
    );
});

test('test reset password link can be requested', function (): void {
    $user = User::factory()->create();

    Notification::fake();

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('password.request'))
            ->fill('email', $user->email)
            ->press('@email-password-reset-link-button')
            ->assertSee('We have emailed your password reset link.')
            ->assertNoConsoleLogs()
    );

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function (): void {
    $user = User::factory()->create();

    Notification::fake();

    Password::sendResetLink(['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification): true {
        assertNoJavaScriptErrorsExceptCspParser(
            visit(route('password.reset', $notification->token))
                ->assertNoConsoleLogs()
        );

        return true;
    });
});

test('password can be reset with valid token', function (): void {
    $user = User::factory()->create();

    Notification::fake();

    Password::sendResetLink(['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user): true {
        visit(route('password.reset', ['token' => $notification->token, 'email' => $user->email]))
            ->fill('password', 'password')
            ->fill('password_confirmation', 'password')
            ->assertValue('email', $user->email)
            ->press('@reset-password-button')
            ->assertUrlIs(route('login'))
            ->assertSee('Your password has been reset.');

        return true;
    });
});
