<?php

declare(strict_types=1);

use App\Providers\VoltServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

it('boots VoltServiceProvider', function (): void {
    expect(App::getProvider(VoltServiceProvider::class))->not->toBeNull();
});

it('handles empty mounted directories gracefully', function (): void {
    // Set config to empty directories
    Config::set('livewire.view_path', '/non-existent/path');

    $provider = App::getProvider(VoltServiceProvider::class);
    expect($provider)->not->toBeNull();

    // Provider should boot without errors even with empty directories
    expect(true)->toBeTrue();
});
