<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

test('password update page is displayed', function (): void {
    actingAs(User::factory()->create());

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('user-password.edit'))
            ->assertSee('Update password')
            ->assertSee('Ensure your account is using a long, random password to stay secure')
    );
});

test('password can be updated', function (): void {
    actingAs($user = User::factory()->create());

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('user-password.edit'))
            ->fill('current_password', 'password')
            ->fill('password', 'new-password')
            ->fill('password_confirmation', 'new-password')
            ->press('@update-password-button')
            ->assertUrlIs(route('user-password.edit'))
    );

    // Verify password was actually updated (this is the real test)
    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function (): void {
    actingAs($user = User::factory()->create());

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('user-password.edit'))
            ->fill('current_password', 'wrong-password')
            ->fill('password', 'new-password')
            ->fill('password_confirmation', 'new-password')
            ->press('@update-password-button')
            ->assertSee('The password is incorrect.')
            ->assertUrlIs(route('user-password.edit'))
    );

    expect(Hash::check('wrong-password', $user->refresh()->password))->toBeFalse();
});
