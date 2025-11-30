<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\ThemeAccentMapperInterface;
use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Services\Theme\ThemeAccentMapper;
use App\Services\Theme\ThemeService;
use Carbon\CarbonImmutable;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ThemeAccentMapperInterface::class, ThemeAccentMapper::class);
        $this->app->singleton(
            ThemeService::class,
            fn (Application $app): ThemeService => new ThemeService(
                accentMapper: $app->make(ThemeAccentMapperInterface::class),
            ),
        );

        FilamentView::registerRenderHook('panels::body.end', fn (): string => Blade::render("@vite('resources/js/app.js')"));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $themeService = $this->app->make(ThemeService::class);

        // Inject theme data into all views
        View::composer('*', function (ViewContract $view) use ($themeService): void {
            $user = auth()->user();
            $settings = $user?->settings;

            // For preview page, check query parameters first, then session data if no authenticated user (T021, FR-011)
            if ($settings === null && request()->is('themes/preview*')) {
                $request = request();
                // Prioritize query parameters over session for fresh page loads
                $sessionTheme = $request->query('theme') ?? session('preview_theme');
                $sessionFlavor = $request->query('flavor') ?? session('preview_flavor');
                $sessionAccent = $request->query('accent') ?? session('preview_accent');

                // Handle 'default' theme - it has Light, Dark, System flavors and accents
                if ($sessionTheme) {
                    $theme = Theme::tryFrom($sessionTheme);

                    if ($theme === Theme::Default) {
                        // Default theme has Light, Dark, System flavors
                        // Default to System if no flavor specified
                        $defaultFlavor = $sessionFlavor ? ThemeFlavor::tryFrom($sessionFlavor) : ThemeFlavor::System;
                        if (! $defaultFlavor || ! in_array($defaultFlavor, Theme::Default->flavors(), true)) {
                            $defaultFlavor = ThemeFlavor::System;
                        }
                        // Default theme uses accents like other themes
                        $defaultAccent = $sessionAccent ? ThemeAccent::tryFrom($sessionAccent) : ThemeAccent::Primary;
                        if (! $defaultAccent) {
                            $defaultAccent = ThemeAccent::Primary;
                        }
                        $settings = new UserSettingsData(
                            theme: Theme::Default,
                            flavor: $defaultFlavor,
                            accent: $defaultAccent,
                        );
                    } elseif ($sessionFlavor && $sessionAccent) {
                        // Other themes require flavor and accent
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
            }

            $themeData = $themeService->resolveThemeData($settings);

            // DEBUG: Log for 'default' theme in testing
            if (app()->environment('testing') && $themeData->theme === Theme::Default) {
                Log::info('View Composer - Default theme resolved', [
                    'theme' => $themeData->theme->value,
                    'flavor' => $themeData->flavor->value,
                    'accent' => $themeData->accent->value,
                    'settings_provided' => $settings !== null,
                    'request_theme' => request()->query('theme'),
                    'session_theme' => session('preview_theme'),
                ]);
            }

            $view->with('themeData', $themeData);

            // Legacy: Ensure themeFlavor is always available for Flux components
            $data = $view->getData();
            if (! isset($data['themeFlavor'])) {
                $view->with('themeFlavor', $themeData->flavor->value);
            }
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => view('partials.theme-script', [
                'themeData' => $themeService->resolveThemeData(auth()->user()?->settings),
            ])->render(),
        );

        $this->configureRateLimiting();
        $this->configureCarbon();
        $this->configureCommands();
        $this->configureModels();
        $this->configurePasswordRules();
        $this->configureUrl();
        $this->configureVite();
    }

    /**
     * Configure rate limiting for theme auto-save (T014, FR-020).
     */
    private function configureRateLimiting(): void
    {
        // Rate limiter for theme auto-save: 10 requests per 60 seconds per user (sliding window)
        RateLimiter::for('theme-auto-save', function (Request $request): Limit {
            $user = $request->user();

            if ($user === null) {
                return Limit::perMinute(10)->by($request->ip());
            }

            return Limit::perMinute(10)->by($user->id);
        });
    }

    /**
     * Configure the application's carbon.
     */
    private function configureCarbon(): void
    {

        Date::use(CarbonImmutable::class);
    }

    /**
     * Configure the application's commands.
     */
    private function configureCommands(): void
    {

        DB::prohibitDestructiveCommands(
            $this->app->environment('production')
            && ! $this->app->runningInConsole()
            && ! $this->app->runningUnitTests()
            && ! $this->app->isDownForMaintenance(),
        );
    }

    /**
     * Configure the application's models.
     */
    private function configureModels(): void
    {

        $isProduction = $this->app->environment('production');
        Model::shouldBeStrict(! $isProduction);
        Model::unguard(! $isProduction);
    }

    /**
     * Configure the application's password rules.
     */
    private function configurePasswordRules(): void
    {

        $isLocalOrTesting = $this->app->environment(['local', 'testing']);
        $shouldRequireUncompromised = ! $this->app->environment(['local', 'testing'])
            && (bool) config('base-platform.security.password_uncompromised', true);

        Password::defaults(function () use ($isLocalOrTesting, $shouldRequireUncompromised): Password {
            $rule = Password::min($isLocalOrTesting ? 8 : 12);

            if ($isLocalOrTesting) {
                // In local/testing, only require letters and minimum length
                $rule->letters();
            } else {
                // In production/staging, require all complexity rules
                $rule->letters()
                    ->numbers()
                    ->symbols()
                    ->mixedCase();
            }

            if ($shouldRequireUncompromised) {
                $rule->uncompromised();
            }

            return $rule;
        });
    }

    /**
     * Configure the application's url.
     */
    private function configureUrl(): void
    {

        if (! $this->app->environment('local')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Configure the application's vite.
     */
    private function configureVite(): void
    {

        Vite::useBuildDirectory('build')
            ->withEntryPoints([
                'resources/js/app.js',
            ]);
    }
}
