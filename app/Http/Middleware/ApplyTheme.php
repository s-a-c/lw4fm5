<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Data\UserSettingsData;
use App\Models\User;
use App\Support\ThemeColorHelper;
use Closure;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ApplyTheme
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var User|null $user */
        $user = Auth::user();

        // Always use default settings if no user is authenticated
        $settings = ($user !== null && $user->settings !== null) ? $user->settings : new UserSettingsData();

        if ($user !== null) {
            // 1. Register PHP Colors (for Filament Rings/Focus states)
            FilamentColor::register(ThemeColorHelper::getFilamentColors($settings));
        }

        // 2. Inject CSS Variables/Hooks into HTML (always, even for guests)
        // Store values in view data so they're available in all Blade templates
        view()->share([
            'themeValue' => $settings->theme->value,
            'flavorValue' => $settings->flavor->value,
            'flavor' => $settings->flavor->value, // Alias for sidebar/flux compatibility
            'accentValue' => $settings->accent->value,
            'isLightValue' => $settings->flavor->isLight(),
        ]);

        // Also register for Filament panels
        FilamentView::registerRenderHook(
            'panels::html.start',
            fn () => view('partials.theme-script', [
                'themeValue' => $settings->theme->value,
                'flavorValue' => $settings->flavor->value,
                'accentValue' => $settings->accent->value,
                'isLightValue' => $settings->flavor->isLight(),
            ])->render()
        );

        return $next($request);
    }
}
