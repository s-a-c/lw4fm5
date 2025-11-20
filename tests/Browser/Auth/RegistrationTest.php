<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;

test('registration screen can be rendered', function (): void {
    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('register'))
            ->assertSee('Create an account')
            ->assertSee('Enter your details below to create your account')
            ->assertNoConsoleLogs()
    );
});

test('new user can be registered', function (): void {
    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('register'))
            ->fill('name', 'Taylor Otwell')
            ->fill('email', 'taylor@laravel.com')
            ->fill('password', 'password')
            ->fill('password_confirmation', 'password')
            ->press('@register-user-button')
            ->assertPathEndsWith('/dashboard')
            ->assertNoConsoleLogs()
    );

    assertAuthenticated();
});

test('new user cannot be registered when email has already been taken', function (): void {
    User::factory()->create([
        'name' => 'Taylor',
        'email' => 'taylor@laravel.com',
    ]);

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('register'))
            ->fill('name', 'Taylor Otwell')
            ->fill('email', 'taylor@laravel.com')
            ->fill('password', 'password')
            ->fill('password_confirmation', 'password')
            ->press('@register-user-button')
            ->assertSee('The email has already been taken.')
            ->assertNoConsoleLogs()
    );

    assertGuest();
});

test('new user cannot be registered when password does not match', function (): void {
    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('register'))
            ->fill('name', 'Taylor Otwell')
            ->fill('email', 'taylor@laravel.com')
            ->fill('password', 'password')
            ->fill('password_confirmation', 'secret')
            ->press('@register-user-button')
            ->assertSee('The password field confirmation does not match.')
            ->assertNoConsoleLogs()
    );

    assertGuest();
});
