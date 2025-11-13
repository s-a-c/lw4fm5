<?php

declare(strict_types=1);

/**
 * Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
 */

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\BasePlatformServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\SupportCustomizationServiceProvider::class,
    App\Providers\FolioServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
];
