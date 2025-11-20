<?php

declare(strict_types=1);

use App\Providers\BroadcastServiceProvider;
use Illuminate\Support\Facades\App;

it('boots BroadcastServiceProvider', function (): void {
    $provider = App::getProvider(BroadcastServiceProvider::class);
    expect($provider)->not->toBeNull();
    expect($provider)->not->toBeNull();
});
