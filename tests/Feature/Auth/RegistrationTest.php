<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\assertAuthenticated;

test('registration screen can be rendered', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'NewPassword123!@#',
        'password_confirmation' => 'NewPassword123!@#',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    assertAuthenticated();
});
