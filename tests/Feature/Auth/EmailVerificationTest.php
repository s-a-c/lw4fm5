<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;

test('email verification screen can be rendered', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->unverified()->create();

    /** @var TestResponse<Response> $response */
    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertOk();
});

test('email can be verified', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1((string) $user->email)]
    );

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

test('email is not verified with invalid hash', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    /** @phpstan-var Tests\TestCase $this */
    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('already verified user visiting verification link is redirected without firing event again', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1((string) $user->email)]
    );

    /** @var TestResponse<Response> $response */
    $response = $this->actingAs($user)->get($verificationUrl);
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');

    $freshUser = $user->fresh();
    expect($freshUser)->not->toBeNull();
    expect($freshUser->hasVerifiedEmail())->toBeTrue();
    Event::assertNotDispatched(Verified::class);
});
