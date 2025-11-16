<?php

declare(strict_types=1);

use App\Providers\BroadcastServiceProvider;
use Illuminate\Support\Facades\App;

it('boots BroadcastServiceProvider', function (): void {
    expect(App::getProvider(BroadcastServiceProvider::class))->not->toBeNull();
});
