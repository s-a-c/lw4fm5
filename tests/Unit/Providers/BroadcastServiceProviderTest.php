<?php

declare(strict_types=1);

use App\Providers\BroadcastServiceProvider;
use Illuminate\Support\Facades\App;

it('boots BroadcastServiceProvider', function (): void {
    $provider = App::getProvider(BroadcastServiceProvider::class);
    assert($provider !== null);
    expect($provider)->not->toBeNull();
});
