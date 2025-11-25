<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;

test('email verification screen can be rendered', function (): void {
    $user = User::factory()->unverified()->create();

    actingAs($user);

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('verification.notice'))
            ->assertSee('Please verify your email address by clicking on the link we just emailed to you.')
            ->assertSee('Resend verification email')
    );
});

test('email can be verified', function (): void {
    $user = User::factory()->unverified()->create();

    actingAs($user);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1((string) $user->email)]
    );

    assertNoJavaScriptErrorsExceptCspParser(
        visit($verificationUrl)
            ->assertUrlIs(route('dashboard'))
            ->assertQueryStringHas('verified', '1')
    );

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('email is not verified with invalid hash', function (): void {
    $user = User::factory()->unverified()->create();

    actingAs($user);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    assertNoJavaScriptErrorsExceptCspParser(
        visit($verificationUrl)
            ->assertSee('403')
            ->assertSee('This action is unauthorized.')
    );

    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('email is not verified with invalid user id', function (): void {
    $user = User::factory()->unverified()->create();

    actingAs($user);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => 123, 'hash' => sha1((string) $user->email)]
    );

    assertNoJavaScriptErrorsExceptCspParser(
        visit($verificationUrl)
            ->assertSee('403')
            ->assertSee('This action is unauthorized.')
    );

    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verified user is redirected to dashboard from verification prompt', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    actingAs($user);

    Event::fake();

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('verification.notice'))
            ->assertUrlIs(route('dashboard'))
    );

    Event::assertNotDispatched(Verified::class);
});

test('already verified user visiting verification link is redirected without firing event again', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    actingAs($user);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1((string) $user->email)]
    );

    assertNoJavaScriptErrorsExceptCspParser(
        visit($verificationUrl)
            ->assertUrlIs(route('dashboard'))
            ->assertQueryStringHas('verified', '1')
    );

    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});
