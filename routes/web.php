<?php

declare(strict_types=1);

use App\Http\Controllers\CspReportingController;
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

    /** @var Illuminate\Routing\Route $profileRoute */
    $profileRoute = Route::livewire('settings/profile', 'settings.profile');
    $profileRoute->name('profile.edit');

    /** @var Illuminate\Routing\Route $passwordRoute */
    $passwordRoute = Route::livewire('settings/password', 'settings.password');
    $passwordRoute->name('user-password.edit');

    /** @var Illuminate\Routing\Route $appearanceRoute */
    $appearanceRoute = Route::livewire('settings/appearance', 'settings.appearance');
    $appearanceRoute->name('appearance.edit');

    $twoFactorMiddleware = when(
        Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
        ['password.confirm'],
        [],
    );
    /** @var array<string> $middlewareArray */
    $middlewareArray = is_array($twoFactorMiddleware) ? $twoFactorMiddleware : [];
    /** @var Illuminate\Routing\Route $twoFactorRoute */
    $twoFactorRoute = Route::livewire('settings/two-factor', 'settings.two-factor');
    $twoFactorRoute->middleware($middlewareArray);
    $twoFactorRoute->name('two-factor.show');
});

Route::post('/csp-report', CspReportingController::class)->name('csp.report');
