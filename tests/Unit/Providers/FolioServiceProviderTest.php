<?php

declare(strict_types=1);

use App\Providers\FolioServiceProvider;
use Illuminate\Support\Facades\App;

it('boots FolioServiceProvider', function (): void {
    expect(App::getProvider(FolioServiceProvider::class))->not->toBeNull();
});
