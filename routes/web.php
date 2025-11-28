<?php

declare(strict_types=1);

use App\Http\Controllers\CspReportingController;
use App\Services\Theme\ThemePerformanceTracker;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Telescope\Telescope;

Route::get('/', fn (): Factory|View => view('welcome'))->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    Route::redirect('settings', 'settings/profile');

    /** @var Illuminate\Routing\Route $profileRoute */
    $profileRoute = Route::livewire('settings/profile', 'settings.profile');
    $profileRoute->name('profile.edit');

    /** @var Illuminate\Routing\Route $passwordRoute */
    $passwordRoute = Route::livewire('settings/password', 'settings.password');
    $passwordRoute->name('user-password.edit');

    /** @var Illuminate\Routing\Route $appearanceRoute */
    $appearanceRoute = Route::livewire('settings/appearance', 'settings.appearance');
    $appearanceRoute->name('appearance.edit');

    $twoFactorMiddleware = when(
        Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
        ['password.confirm'],
        [],
    );
    /** @var array<string> $middlewareArray */
    $middlewareArray = is_array($twoFactorMiddleware) ? $twoFactorMiddleware : [];
    /** @var Illuminate\Routing\Route $twoFactorRoute */
    $twoFactorRoute = Route::livewire('settings/two-factor', 'settings.two-factor');
    $twoFactorRoute->middleware($middlewareArray);
    $twoFactorRoute->name('two-factor.show');
});

Route::post('/csp-report', CspReportingController::class)->name('csp.report');

// Theme preview page (public, no auth required) - Livewire SPA component
/** @var Illuminate\Routing\Route $previewRoute */
$previewRoute = Route::livewire('themes/preview', 'themes.preview');
$previewRoute->name('themes.preview');

// Preview page interaction tracking (T027a, T027g, FR-036, FR-103)
Route::post('/themes/preview/interaction', function (): JsonResponse {
    $user = auth()->user();
    $sessionId = session()->getId();
    $interactionType = request()->input('interaction_type');
    $interactionValue = request()->input('interaction_value');
    $theme = request()->input('theme');
    $flavor = request()->input('flavor');
    $accent = request()->input('accent');
    $timestamp = request()->input('timestamp');

    // Track usage patterns (T027g, FR-103)
    $sessionKey = "theme:preview:session:{$sessionId}";
    $sessionData = Cache::get($sessionKey, [
        'interaction_count' => 0,
        'first_interaction' => now()->toIso8601String(),
        'themes_previewed' => [],
        'last_interaction' => null,
    ]);

    $sessionData['interaction_count']++;
    $sessionData['last_interaction'] = now()->toIso8601String();

    // Track unique themes previewed
    $themeKey = "{$theme}:{$flavor}:{$accent}";
    if (! in_array($themeKey, $sessionData['themes_previewed'], true)) {
        $sessionData['themes_previewed'][] = $themeKey;
    }

    // Store session data (24-hour expiration)
    Cache::put($sessionKey, $sessionData, now()->addDay());

    // Track interaction type frequency (T027g, FR-103)
    $interactionTypeKey = "theme:preview:interaction_type:{$interactionType}";
    Cache::increment($interactionTypeKey, 1);

    // Track theme popularity (T027g, FR-103)
    $themePopularityKey = "theme:preview:popularity:{$theme}:{$flavor}:{$accent}";
    Cache::increment($themePopularityKey, 1);

    Log::info('Preview page interaction', [
        'event_type' => 'preview_interaction',
        'timestamp' => $timestamp ?? now()->toIso8601String(),
        'timezone' => config('app.timezone'),
        'user_id' => $user?->id,
        'session_id' => $sessionId,
        'request_id' => request()->header('X-Request-ID'),
        'interaction_type' => $interactionType,
        'interaction_value' => $interactionValue,
        'theme' => $theme,
        'flavor' => $flavor,
        'accent' => $accent,
        'session_interaction_count' => $sessionData['interaction_count'], // T027g, FR-103
        'themes_previewed_count' => count($sessionData['themes_previewed']), // T027g, FR-103
        'session_duration_seconds' => $sessionData['last_interaction']
            ? (int) (now()->diffInSeconds(Date::parse($sessionData['first_interaction'])))
            : 0, // T027g, FR-103
        'page_url' => request()->input('page_url'), // T027g, FR-103 - navigation tracking
        'referrer' => request()->input('referrer'), // T027g, FR-103 - conversion tracking
        'performance' => request()->input('performance'), // T027g, FR-103 - performance metrics
    ]);

    // Tag Telescope entry for filtering (T027a)
    if (class_exists(Telescope::class)) {
        Telescope::tag(fn (): array => ['theme:preview_interaction', 'theme:event']);
    }

    return response()->json(['status' => 'success']);
})->name('themes.preview.interaction');

// Performance tracking endpoint (T027e, FR-101)
Route::post('/themes/performance', function (): JsonResponse {
    $operation = request()->input('operation');
    $domUpdateTime = (float) request()->input('dom_update_time', 0);
    $correlationId = request()->input('correlation_id');

    // Get matching server-side metrics using correlation ID (T027e, FR-101)
    $serverMetrics = [
        'database_query_time' => 0.0,
        'total_time' => $domUpdateTime, // Default to DOM time if no server metrics
    ];

    if ($correlationId) {
        $metricsKey = "theme:performance:server:{$correlationId}";
        $metrics = Cache::get($metricsKey);
        if ($metrics) {
            $serverMetrics = $metrics;
            // Clean up after retrieval
            Cache::forget($metricsKey);
        }
    }

    // Record complete performance metrics (T027e, FR-101)
    ThemePerformanceTracker::record(
        operation: $operation,
        domUpdateTime: $domUpdateTime,
        databaseQueryTime: $serverMetrics['database_query_time'] ?? 0.0,
        totalTime: max($serverMetrics['total_time'] ?? $domUpdateTime, $domUpdateTime),
    );

    return response()->json(['status' => 'success']);
})->name('themes.performance');
