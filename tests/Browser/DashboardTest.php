<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;

test('guests are redirected to the login page', function (): void {
    visit(route('dashboard'))
        ->assertUrlIs(route('login'))
        ->assertNoConsoleLogs()
        ->assertNoJavaScriptErrors()
        ->assertSee('Log in to your account')
        ->assertSee('Enter your email and password below to log in');
});

test('authenticated users can visit the dashboard', function (): void {
    actingAs(User::factory()->create());

    visit(route('dashboard'))
        ->assertUrlIs(route('dashboard'))
        ->assertNoConsoleLogs()
        ->assertNoJavaScriptErrors();
});
