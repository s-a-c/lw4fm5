<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

test('password can be updated', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    /** @phpstan-var Testable $response */
    $response = Livewire::test('settings.password')
        ->set('current_password', 'password')
        ->set('password', 'NewPassword123!@#')
        ->set('password_confirmation', 'NewPassword123!@#')
        ->call('updatePassword');

    $response->assertHasNoErrors();

    expect(Hash::check('NewPassword123!@#', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    /** @phpstan-var Testable $response */
    $response = Livewire::test('settings.password')
        ->set('current_password', 'wrong-password')
        ->set('password', 'NewPassword123!@#')
        ->set('password_confirmation', 'NewPassword123!@#')
        ->call('updatePassword');

    $response->assertHasErrors(['current_password']);
});
