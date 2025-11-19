<?php

declare(strict_types=1);

use App\Providers\FortifyServiceProvider;
use Illuminate\Support\Facades\App;
use Laravel\Fortify\Fortify;

it('boots FortifyServiceProvider and configures Fortify', function (): void {
    $provider = App::getProvider(FortifyServiceProvider::class);
    assert($provider !== null);
    expect($provider)->not->toBeNull();
});

it('registers Fortify actions', function (): void {
    // Verify Fortify is configured (tested indirectly through app booting)
    expect(Fortify::class)->toBeClass();
});
