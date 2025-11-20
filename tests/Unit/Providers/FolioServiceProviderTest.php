<?php

declare(strict_types=1);

use App\Providers\FolioServiceProvider;
use Illuminate\Support\Facades\App;

it('boots FolioServiceProvider', function (): void {
    $provider = App::getProvider(FolioServiceProvider::class);
    expect($provider)->not->toBeNull();
    expect($provider)->not->toBeNull();
});
