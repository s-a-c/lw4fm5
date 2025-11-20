<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Fortify\Features;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    if (! Features::canManageTwoFactorAuthentication()) {
        skip('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);
});

test('two factor settings page can be rendered', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->withoutTwoFactor()->create();

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'));
    $response->assertOk()
        ->assertSee('Two Factor Authentication')
        ->assertSee('Disabled');
});

test('two factor settings page requires password confirmation when enabled', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->actingAs($user)
        ->get(route('two-factor.show'));

    $response->assertRedirect(route('password.confirm'));
});

test('two factor settings page returns forbidden response when two factor is disabled', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    config(['fortify.features' => []]);

    $user = User::factory()->create();

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'));

    $response->assertForbidden();
});

test('two factor authentication disabled when confirmation abandoned between requests', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();

    $user->forceFill([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => null,
    ])->save();

    $this->actingAs($user);

    /** @phpstan-var Testable $component */
    $component = Livewire::test('settings.two-factor');

    $component->assertSet('twoFactorEnabled', false);

    assertDatabaseHas('users', [
        'id' => $user->id,
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
    ]);
});
