<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Models\User;
use App\Services\Theme\ThemeService;
use App\Support\ThemeColorHelper;
use Closure;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final readonly class ApplyTheme
{
    public function __construct(
        private ThemeService $themeService,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        /** @var User|null $user */
        $user = Auth::user();

        $settings = $user?->settings;

        // For preview page, check session data if no authenticated user (T021, FR-011)
        if ($settings === null && $request->is('themes/preview*')) {
            $sessionTheme = session('preview_theme');
            $sessionFlavor = session('preview_flavor');
            $sessionAccent = session('preview_accent');

            if ($sessionTheme && $sessionFlavor && $sessionAccent) {
                $theme = Theme::tryFrom($sessionTheme);
                $flavor = ThemeFlavor::tryFrom($sessionFlavor);
                $accent = ThemeAccent::tryFrom($sessionAccent);

                if ($theme && $flavor && $accent) {
                    $settings = new UserSettingsData(
                        theme: $theme,
                        flavor: $flavor,
                        accent: $accent,
                    );
                }
            }
        }

        $themeData = $this->themeService->resolveThemeData($settings);

        if ($user !== null) {
            // 1. Register PHP Colors (for Filament Rings/Focus states)
            FilamentColor::register(
                ThemeColorHelper::getFilamentColors(
                    new UserSettingsData(
                        theme: $themeData->theme,
                        flavor: $themeData->flavor,
                        accent: $themeData->accent,
                    ),
                ),
            );
        }

        // 2. Share theme data for Blade templates (Flux compatibility + legacy variables)
        view()->share([
            'themeData' => $themeData,
            'themeValue' => $themeData->theme->value,
            'flavorValue' => $themeData->flavor->value,
            'flavor' => $themeData->flavor->value, // Alias for sidebar/flux compatibility
            'themeFlavor' => $themeData->flavor->value,
            'accentValue' => $themeData->accent->value,
            'isLightValue' => $themeData->isLight(),
        ]);

        return $next($request);
    }
}
