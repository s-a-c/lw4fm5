<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;

test('login screen can be rendered', function (): void {
    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('login'))
            ->assertSee('Log in to your account')
            ->assertSee('Enter your email and password below to log in')
    );
});

test('users can authenticate using the login screen', function (): void {
    $user = User::factory()->withoutTwoFactor()->create();

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('login'))
            ->fill('email', $user->email)
            ->fill('password', 'password')
            ->press('@login-button')
            ->assertUrlIs(route('dashboard'))
    );

    assertAuthenticated();
});

test('users can not authenticate with invalid password', function (): void {
    $user = User::factory()->create();

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('login'))
            ->fill('email', $user->email)
            ->fill('password', 'wrong-password')
            ->press('@login-button')
            ->assertUrlIs(route('login'))
            ->assertSee('These credentials do not match our records.')
    );

    assertGuest();
});

test('users can logout', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('dashboard'))
            ->click('@sidebar-menu-button')
            ->click('@logout-button')
            ->assertUrlIs(route('home'))
    );

    assertGuest();
});
