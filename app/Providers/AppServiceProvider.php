<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
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
        FilamentView::registerRenderHook('panels::body.end', fn (): string => Blade::render("@vite('resources/js/app.js')"));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCarbon();
        $this->configureCommands();
        $this->configureModels();
        $this->configurePasswordRules();
        $this->configureUrl();
        $this->configureVite();
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
