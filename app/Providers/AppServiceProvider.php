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
            $this->app->isProduction()
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

        Model::shouldBeStrict(! $this->app->isProduction());
        Model::unguard(! $this->app->isProduction());
    }

    /**
     * Configure the application's password rules.
     */
    private function configurePasswordRules(): void
    {

        if (! $this->app->isLocal()) {
            Password::defaults(fn () => Password::min(12)
                ->letters()
                ->numbers()
                ->symbols()
                ->mixedCase()
                ->uncompromised());
        } else {
            Password::defaults(fn () => Password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
                ->mixedCase());
        }

        // config(['auth.password_timeout' => 60])
    }

    /**
     * Configure the application's url.
     */
    private function configureUrl(): void
    {

        if (! $this->app->isLocal()) {
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
