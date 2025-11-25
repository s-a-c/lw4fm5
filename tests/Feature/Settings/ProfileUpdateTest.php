<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

test('profile page is displayed', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $this->actingAs($user = User::factory()->create());

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->get(route('profile.edit'));
    $response->assertOk();
});

test('profile information can be updated', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @phpstan-var Testable $response */
    $response = Livewire::test('settings.profile')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @phpstan-var Testable $response */
    $response = Livewire::test('settings.profile')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $verified = $user->refresh()->email_verified_at;
    assert($verified !== null);
    expect($verified)->not->toBeNull();
});

test('user can delete their account', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @phpstan-var Testable $response */
    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response->assertHasNoErrors();
    $response->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @phpstan-var Testable $response */
    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    $freshUser = $user->fresh();
    assert($freshUser !== null);
    expect($freshUser)->not->toBeNull();
});
