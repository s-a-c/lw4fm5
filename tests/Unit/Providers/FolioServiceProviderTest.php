<?php

declare(strict_types=1);

use App\Providers\FolioServiceProvider;
use Illuminate\Support\Facades\App;

it('boots FolioServiceProvider', function (): void {
    $provider = App::getProvider(FolioServiceProvider::class);
    assert($provider !== null);
    expect($provider)->not->toBeNull();
});
