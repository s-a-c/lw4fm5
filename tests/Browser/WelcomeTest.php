<?php

declare(strict_types=1);

test('welcome screen can be rendered', function (): void {
    assertNoJavaScriptErrorsExceptCspParser(
        visit('/')
            ->assertSee('Let\'s get started')
            ->assertSee('Log In')
            ->assertSee('Register')
    );
});

test('guests can browse to register page from welcome page', function (): void {
    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('home'))
            ->click('Register')
            ->assertUrlIs(route('register'))
            ->assertSee('Create an account')
            ->assertSee('Enter your details below to create your account')
    );
});

test('guests can browse to login page from welcome page', function (): void {
    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('home'))
            ->click('Log in')
            ->assertUrlIs(route('login'))
            ->assertSee('Log in to your account')
            ->assertSee('Enter your email and password below to log in')
    );
});
