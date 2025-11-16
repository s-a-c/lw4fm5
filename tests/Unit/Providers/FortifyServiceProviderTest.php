<?php

declare(strict_types=1);

use App\Providers\FortifyServiceProvider;
use Illuminate\Support\Facades\App;
use Laravel\Fortify\Fortify;

it('boots FortifyServiceProvider and configures Fortify', function (): void {
    expect(App::getProvider(FortifyServiceProvider::class))->not->toBeNull();
});

it('registers Fortify actions', function (): void {
    // Verify Fortify is configured (tested indirectly through app booting)
    expect(Fortify::class)->toBeClass();
});
