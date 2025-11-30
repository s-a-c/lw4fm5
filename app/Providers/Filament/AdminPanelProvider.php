<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\ApplyTheme;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')

            // -----------------------------------------------------------
            // 1. LOAD THEME ENGINE CSS
            // -----------------------------------------------------------
            // This loads the unified app.css which includes Filament base theme,
            // theme engine, Zinc Bridge, and all theme definitions.
            ->viteTheme('resources/css/app.css')

            // FALLBACK COLORS:
            // These act as defaults before the user authenticates.
            // The Middleware will hook in and overwrite these later.
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Zinc,
            ])

            ->sidebarCollapsibleOnDesktop()
            ->profile()

            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,

                // -------------------------------------------------------
                // 2. REGISTER THEME MIDDLEWARE
                // -------------------------------------------------------
                // This injects the data-theme, data-flavor, and data-accent
                // attributes into the HTML tag and registers the semantic
                // PHP colors (Primary, Danger, etc.) for this request.
                ApplyTheme::class,
            ]);
    }
}
