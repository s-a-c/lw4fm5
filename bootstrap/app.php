<?php

declare(strict_types=1);

use Illuminate\Support\Env;

if ((Env::get('APP_ENV')) === 'local') {
    error_reporting(error_reporting() & ~E_DEPRECATED & ~E_USER_DEPRECATED);
}

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('platform:validate-profiles --all')
            ->weeklyOn(1, '03:00')
            ->description('Weekly validation for native and container profiles');

        $schedule->command('policy:checksum-monitor')
            ->dailyAt('02:00')
            ->description('Nightly policy acknowledgement checksum monitor');

        $schedule->call(function (): void {
            $result = Process::path(base_path())->run('composer workflow:heavy');

            if ($result->failed()) {
                throw new RuntimeException('Nightly heavy suite run failed: '.$result->errorOutput());
            }
        })->dailyAt('02:30')->description('Nightly heavy suite run');

        $schedule->call(function (): void {
            Artisan::call('policy:checksum-monitor');
            Artisan::call('platform:validate-profiles --all');
        })->weeklyOn(5, '04:00')->description('Weekly checksum and validation bundle');

        $schedule->command('platform:dependency-review')
            ->monthlyOn(1, '05:00')
            ->when(fn (): bool => (bool) config('base-platform.schedules.monthly', true))
            ->description('Monthly dependency review governance report');

        $schedule->command('platform:dependency-review-performance-report')
            ->monthlyOn(1, '05:30')
            ->when(fn (): bool => (bool) config('base-platform.schedules.monthly', true))
            ->description('Monthly dependency review performance summary');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware
            ->web(append: [
                \App\Http\Middleware\ApplyTheme::class,
            ])
            ->alias([
                'abilities' => CheckAbilities::class,
                'ability' => CheckForAnyAbility::class,
            ])
            ->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
