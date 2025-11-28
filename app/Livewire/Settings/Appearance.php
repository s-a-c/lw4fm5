<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Contracts\ThemeAccentMapperInterface;
use App\Data\ThemeData;
use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Services\Theme\ThemePerformanceTracker;
use App\Services\Theme\ThemeSecurityAuditLogger;
use App\Services\Theme\ThemeService;
use App\Support\ThemeErrorLogger;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Js;
use Laravel\Telescope\Telescope;
use Livewire\Component;
use Throwable;

final class Appearance extends Component
{
    public string $theme;

    public string $flavor;

    public string $accent;

    /** @var array<int, ThemeFlavor> */
    public array $availableFlavors = [];

    /** @var array<int, ThemeAccent> */
    public array $availableAccents = [];

    public bool $showReset = false;

    private ThemeService $themeService;

    private ThemeAccentMapperInterface $accentMapper;

    private bool $isSaving = false;

    private bool $queuedSave = false;

    private int $retryCount = 0;

    public function boot(
        ThemeService $themeService,
        ThemeAccentMapperInterface $accentMapper,
    ): void {
        $this->themeService = $themeService;
        $this->accentMapper = $accentMapper;
    }

    public function mount(): void
    {
        $themeData = $this->resolveThemeData();

        $this->theme = $themeData->theme->value;
        $this->flavor = $themeData->flavor->value;
        $this->accent = $themeData->accent->value;

        $this->refreshAvailableOptions();
        $this->updateResetVisibility();
    }

    public function updatedTheme(string $value): void
    {
        // Validate and correct theme value immediately (prevents XSS/invalid values)
        $this->theme = $this->safeThemeFromValue($value)->value;
        $this->refreshAvailableOptions();
        $this->afterPreferenceChanged();
    }

    public function updatedFlavor(string $value): void
    {
        $this->flavor = $this->valueWithinFlavors($value);
        $this->afterPreferenceChanged();
    }

    public function updatedAccent(string $value): void
    {
        $this->accent = $this->valueWithinAccents($value);
        $this->afterPreferenceChanged();
    }

    public function resetToDefault(): void
    {
        $defaults = new UserSettingsData();

        $this->theme = $defaults->theme->value;
        $this->flavor = $defaults->flavor->value;
        $this->accent = $defaults->accent->value;

        $this->refreshAvailableOptions();
        $this->afterPreferenceChanged();
    }

    public function performSave(): void
    {
        // Rate limiting: 10 requests per 60 seconds per user (T014, FR-020)
        $this->enforceRateLimit();

        if ($this->isSaving) {
            $this->queuedSave = true;

            return;
        }

        $this->isSaving = true;
        $this->queuedSave = false;
        $this->retryCount = 0;

        $this->attemptPersist();
    }

    public function retrySave(): void
    {
        if (! $this->isSaving) {
            $this->isSaving = true;
        }

        $this->attemptPersist();
    }

    public function render(): Factory|View
    {
        return view('livewire.settings.appearance');
    }

    private function resolveThemeData(): ThemeData
    {
        $user = Auth::user();

        return $this->themeService->resolveThemeData($user?->settings);
    }

    private function refreshAvailableOptions(): void
    {
        $this->updateAvailableFlavors();
        $this->updateAvailableAccents();
    }

    private function updateAvailableFlavors(): void
    {
        $theme = $this->safeThemeFromValue($this->theme);
        $this->availableFlavors = $theme->flavors();
        $this->flavor = $this->valueWithinFlavors($this->flavor);
    }

    private function updateAvailableAccents(): void
    {
        $theme = $this->safeThemeFromValue($this->theme);
        try {
            $this->availableAccents = $this->accentMapper->getAvailableAccents($theme);
        } catch (Throwable) {
            // If mapper fails, use default accents (T018c, FR-103)
            $this->availableAccents = [ThemeAccent::Primary];
        }
        $this->accent = $this->valueWithinAccents($this->accent);
    }

    private function valueWithinFlavors(string $value): string
    {
        foreach ($this->availableFlavors as $flavor) {
            if ($flavor->value === $value) {
                return $value;
            }
        }

        return $this->availableFlavors[0]->value;
    }

    private function valueWithinAccents(string $value): string
    {
        foreach ($this->availableAccents as $accent) {
            if ($accent->value === $value) {
                return $value;
            }
        }

        return $this->availableAccents[0]->value;
    }

    private function afterPreferenceChanged(): void
    {
        $this->updateResetVisibility();
        $this->dispatchThemeDom();
        $this->queueSave();
    }

    private function queueSave(): void
    {
        $this->queuedSave = true;

        if (app()->runningUnitTests()) {
            $this->performSave();

            return;
        }

        $this->dispatch(
            'appearance-save-debounced',
            componentId: $this->getId(),
        );
    }

    private function updateResetVisibility(): void
    {
        $defaults = new UserSettingsData();

        $this->showReset = ! (
            $this->theme === $defaults->theme->value
            && $this->flavor === $defaults->flavor->value
            && $this->accent === $defaults->accent->value
        );
    }

    private function dispatchThemeDom(): void
    {
        // Dispatch event for JavaScript listeners (handles DOM updates)
        $this->dispatch(
            'theme-updated',
            theme: $this->theme,
            flavor: $this->flavor,
            accent: $this->accent,
        );

        // Immediate DOM update via $this->js() for instant visual feedback (T013, FR-081)
        // This provides synchronous execution before the event listener processes
        $this->js(
            sprintf(
                'if (typeof window !== "undefined" && window.__liveThemePreview) { window.__liveThemePreview(%s); }',
                Js::from([
                    'theme' => $this->theme,
                    'flavor' => $this->flavor,
                    'accent' => $this->accent,
                ])
            )
        );
    }

    private function attemptPersist(): void
    {
        $startTime = microtime(true);

        try {
            $user = Auth::user();

            if ($user === null) {
                $this->markSaveComplete();

                return;
            }

            $oldSettings = $user->settings;

            $databaseStartTime = microtime(true);
            DB::transaction(function () use ($user): void {
                $user->settings = new UserSettingsData(
                    theme: $this->safeThemeFromValue($this->theme),
                    flavor: ThemeFlavor::from($this->flavor),
                    accent: ThemeAccent::from($this->accent),
                );

                $user->save();
            });
            $databaseEndTime = microtime(true);
            $databaseQueryTime = ($databaseEndTime - $databaseStartTime) * 1000; // Convert to milliseconds

            $totalTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

            // Generate correlation ID for matching client-side metrics (T027e, FR-101)
            $correlationId = uniqid('perf_', true);

            // Store server-side metrics with correlation ID (T027e, FR-101)
            // Client-side will send DOM update time with this correlation ID
            $metricsKey = "theme:performance:server:{$correlationId}";
            Cache::put($metricsKey, [
                'database_query_time' => $databaseQueryTime,
                'total_time' => $totalTime,
                'correlation_id' => $correlationId,
            ], now()->addMinutes(5)); // Short expiration, just for client retrieval

            // Send correlation ID to client via Livewire event (T027e, FR-101)
            $this->dispatch('theme-performance-correlation', correlationId: $correlationId);

            // Record server-side performance metrics (T027e, FR-101)
            // DOM update time will be sent from client-side and combined with these metrics
            ThemePerformanceTracker::record(
                operation: 'theme_save',
                domUpdateTime: 0.0, // Will be updated from client-side
                databaseQueryTime: $databaseQueryTime,
                totalTime: $totalTime,
            );

            // Record theme_changed event (T027a, FR-036)
            $this->recordThemeChanged($oldSettings);

            // Security audit logging (T027l, FR-077)
            $newSettings = new UserSettingsData(
                theme: $this->safeThemeFromValue($this->theme),
                flavor: ThemeFlavor::from($this->flavor),
                accent: ThemeAccent::from($this->accent),
            );
            ThemeSecurityAuditLogger::logThemeChange($oldSettings, $newSettings);

            $this->markSaveComplete();
        } catch (Throwable $exception) {
            $this->scheduleRetry($exception);
        }
    }

    private function markSaveComplete(): void
    {
        $queued = $this->queuedSave;

        $this->isSaving = false;
        $this->queuedSave = false;
        $this->retryCount = 0;

        if ($queued) {
            $this->queueSave();
        }
    }

    private function scheduleRetry(Throwable $exception): void
    {
        $this->retryCount++;

        ThemeErrorLogger::warning(
            'Theme save failed',
            $exception,
            [
                'retry_count' => $this->retryCount,
                'retry_max' => 5,
                'event_type' => 'save_retry',
                'theme' => $this->theme,
                'flavor' => $this->flavor,
                'accent' => $this->accent,
            ]
        );

        if ($this->retryCount > 5) {
            $this->isSaving = false;
            $this->queuedSave = true;

            $this->dispatch(
                'appearance-toast',
                variant: 'error',
                message: __('Theme update failed. Please try again.'),
            );

            return;
        }

        $delayMs = 1000 * (2 ** ($this->retryCount - 1));

        $this->dispatch(
            'appearance-toast',
            variant: 'info',
            message: __('Retrying theme update...'),
        );

        $this->dispatch(
            'appearance-save-retry',
            componentId: $this->getId(),
            delayMs: $delayMs,
        );
    }

    private function safeThemeFromValue(string $value): Theme
    {
        try {
            return Theme::from($value);
        } catch (Throwable $exception) {
            ThemeErrorLogger::warning(
                'Invalid theme value detected',
                $exception,
                [
                    'invalid_value' => $value,
                    'event_type' => 'validation_error',
                ]
            );

            // Security audit logging (T027l, FR-077)
            ThemeSecurityAuditLogger::logFailedValidation(
                invalidTheme: null, // Theme enum doesn't exist for invalid value
                invalidFlavor: null,
                invalidAccent: null,
                reason: "Invalid theme value: {$value}"
            );

            return Theme::Catppuccin;
        }
    }

    /**
     * Enforce rate limiting for theme auto-save (T014, FR-020).
     * Sliding window: 10 requests per 60 seconds per user.
     */
    private function enforceRateLimit(): void
    {
        $user = Auth::user();
        $key = $user?->id ?? request()->ip();
        $rateLimitKey = 'theme-auto-save:'.$key;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $attempts = RateLimiter::attempts($rateLimitKey);

            // Security audit logging (T027l, FR-077)
            ThemeSecurityAuditLogger::logRateLimitViolation($rateLimitKey, 10, $attempts);

            $this->dispatch(
                'appearance-toast',
                variant: 'error',
                message: __('Too many theme update attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]),
            );

            throw new ThrottleRequestsException(
                __('Too many theme update attempts. Please try again in :seconds seconds.', ['seconds' => $seconds])
            );
        }

        RateLimiter::hit($rateLimitKey, 60);
    }

    /**
     * Record theme_changed event in Telescope (T027a, FR-036).
     */
    private function recordThemeChanged(?UserSettingsData $oldSettings): void
    {
        $user = Auth::user();
        $newTheme = $this->safeThemeFromValue($this->theme);
        $newFlavor = ThemeFlavor::from($this->flavor);
        $newAccent = ThemeAccent::from($this->accent);

        // Record as info log (T027b, FR-038) - Telescope will capture this
        Log::info('Theme changed', [
            'event_type' => 'theme_changed',
            'timestamp' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
            'user_id' => $user?->id,
            'session_id' => session()->getId(),
            'request_id' => request()->header('X-Request-ID'),
            'old_theme' => $oldSettings?->theme->value,
            'old_flavor' => $oldSettings?->flavor->value,
            'old_accent' => $oldSettings?->accent->value,
            'new_theme' => $newTheme->value,
            'new_flavor' => $newFlavor->value,
            'new_accent' => $newAccent->value,
        ]);

        // Tag Telescope entry for filtering (T027a)
        if (class_exists(Telescope::class)) {
            Telescope::tag(fn (): array => ['theme:theme_changed', 'theme:event']);
        }
    }
}
