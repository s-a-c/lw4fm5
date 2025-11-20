<?php

declare(strict_types=1);

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', fn (): Factory|View => view('welcome'))->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'settings.profile')->name('profile.edit');
    Route::livewire('settings/password', 'settings.password')->name('user-password.edit');
    Route::livewire('settings/appearance', 'settings.appearance')->name('appearance.edit');

    $twoFactorMiddleware = when(
        Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
        ['password.confirm'],
        [],
    );
    /** @var array<string> $middlewareArray */
    $middlewareArray = is_array($twoFactorMiddleware) ? $twoFactorMiddleware : [];
    Route::livewire('settings/two-factor', 'settings.two-factor')
        ->middleware($middlewareArray)
        ->name('two-factor.show');
});
