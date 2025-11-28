<?php

declare(strict_types=1);

namespace App\Services\Theme;

use App\Contracts\ThemeAccentMapperInterface;
use App\Data\ThemeData;
use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Support\ThemeErrorLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Telescope\Telescope;
use Throwable;

final readonly class ThemeService
{
    public function __construct(
        private ThemeAccentMapperInterface $accentMapper,
    ) {}

    /**
     * Resolve theme data from user settings, validating and correcting as needed.
     *
     * Validates on every access (whenever settings are read: View Composer,
     * Livewire mount, direct model access, etc.).
     *
     * @param  UserSettingsData|null  $settings  User settings or null for defaults
     * @return ThemeData Validated and corrected theme data
     */
    public function resolveThemeData(?UserSettingsData $settings): ThemeData
    {
        // Default values
        $defaultTheme = Theme::Catppuccin;
        $defaultFlavor = ThemeFlavor::Mocha;
        $defaultAccent = ThemeAccent::Primary;

        // If no settings, return defaults
        if (! $settings instanceof UserSettingsData) {
            return new ThemeData(
                theme: $defaultTheme,
                flavor: $defaultFlavor,
                accent: $defaultAccent,
            );
        }

        $theme = $settings->theme;
        $flavor = $settings->flavor;
        $accent = $settings->accent;

        $originalTheme = $theme;
        $originalFlavor = $flavor;
        $originalAccent = $accent;
        $wasCorrected = false;

        // Validate theme enum
        if (! $this->isValidTheme($theme)) {
            $theme = $defaultTheme;
            $flavor = $defaultFlavor;
            $accent = $defaultAccent;
            $wasCorrected = true;
        }

        // Validate flavor enum
        if (! $this->isValidFlavor($flavor)) {
            $flavor = $defaultFlavor;
            $accent = $defaultAccent;
            $wasCorrected = true;
        }

        // Handle None theme (system default) - no flavors or accents
        if ($theme === Theme::None) {
            return new ThemeData(
                theme: Theme::None,
                flavor: ThemeFlavor::Default, // Placeholder, not used
                accent: ThemeAccent::Primary, // Placeholder, not used
            );
        }

        // Validate theme/flavor combination
        $availableFlavors = $theme->flavors();
        if (! in_array($flavor, $availableFlavors, true)) {
            // Use first available flavor for this theme, not the default
            $flavor = $availableFlavors[0] ?? $defaultFlavor;
            $accent = $defaultAccent;
            $wasCorrected = true;
        }

        // Validate accent enum
        if (! $this->isValidAccent($accent)) {
            $accent = $defaultAccent;
            $wasCorrected = true;
        }

        // Validate accent for theme using ThemeAccentMapper
        try {
            if (! $this->accentMapper->validateAccent($theme, $accent)) {
                // Accent not valid for theme, try Primary first
                $availableAccents = $this->accentMapper->getAvailableAccents($theme);
                if (in_array(ThemeAccent::Primary, $availableAccents, true)) {
                    $accent = ThemeAccent::Primary;
                } elseif (count($availableAccents) > 0) {
                    // Fallback to first available accent
                    $accent = $availableAccents[0];
                } else {
                    // No accents available, use default
                    $accent = $defaultAccent;
                }
                $wasCorrected = true;
            }
        } catch (Throwable $e) {
            // ThemeAccentMapper service failure - fallback to default theme
            ThemeErrorLogger::error(
                'ThemeAccentMapper service failure',
                $e,
                [
                    'theme' => $theme->value,
                    'accent' => $accent->value,
                    'event_type' => 'service_failure',
                ]
            );

            return new ThemeData(
                theme: $defaultTheme,
                flavor: $defaultFlavor,
                accent: $defaultAccent,
            );
        }

        // Record validation_corrected event if correction occurred (T027a, FR-036)
        if ($wasCorrected) {
            $this->recordValidationCorrected($originalTheme, $originalFlavor, $originalAccent, $theme, $flavor, $accent);

            // Security audit logging (T027l, FR-077)
            ThemeSecurityAuditLogger::logFailedValidation(
                invalidTheme: $originalTheme,
                invalidFlavor: $originalFlavor,
                invalidAccent: $originalAccent,
                reason: 'Invalid theme/flavor/accent combination'
            );
        }

        return new ThemeData(
            theme: $theme,
            flavor: $flavor,
            accent: $accent,
        );
    }

    /**
     * Validate theme enum value.
     */
    private function isValidTheme(Theme $theme): bool
    {
        return Theme::tryFrom($theme->value) !== null;
    }

    /**
     * Validate flavor enum value.
     */
    private function isValidFlavor(ThemeFlavor $flavor): bool
    {
        return ThemeFlavor::tryFrom($flavor->value) !== null;
    }

    /**
     * Validate accent enum value.
     */
    private function isValidAccent(ThemeAccent $accent): bool
    {
        return ThemeAccent::tryFrom($accent->value) !== null;
    }

    /**
     * Record validation_corrected event in Telescope (T027a, FR-036).
     * Track invalid theme combination frequency (T027f, FR-102).
     */
    private function recordValidationCorrected(
        Theme $originalTheme,
        ThemeFlavor $originalFlavor,
        ThemeAccent $originalAccent,
        Theme $correctedTheme,
        ThemeFlavor $correctedFlavor,
        ThemeAccent $correctedAccent,
    ): void {
        $user = Auth::user();

        // Track correction frequency (T027f, FR-102)
        $correctionKey = sprintf(
            'theme:correction:%s:%s:%s->%s:%s:%s',
            $originalTheme->value,
            $originalFlavor->value,
            $originalAccent->value,
            $correctedTheme->value,
            $correctedFlavor->value,
            $correctedAccent->value
        );

        // Increment correction count (24-hour window)
        $correctionCount = Cache::increment($correctionKey, 1);
        if ($correctionCount === 1) {
            // First occurrence, set expiration
            Cache::put($correctionKey, 1, now()->addDay());
        }

        // Record as warning log (T027b, FR-038) - Telescope will capture this
        Log::warning('Theme validation corrected', [
            'event_type' => 'validation_corrected',
            'timestamp' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
            'user_id' => $user?->id,
            'session_id' => session()->getId(),
            'request_id' => request()->header('X-Request-ID'),
            'original_theme' => $originalTheme->value,
            'original_flavor' => $originalFlavor->value,
            'original_accent' => $originalAccent->value,
            'corrected_theme' => $correctedTheme->value,
            'corrected_flavor' => $correctedFlavor->value,
            'corrected_accent' => $correctedAccent->value,
            'correction_frequency' => $correctionCount, // T027f, FR-102
            'correction_key' => $correctionKey, // For debugging/analysis
        ]);

        // Tag Telescope entry for filtering (T027a)
        if (class_exists(Telescope::class)) {
            Telescope::tag(fn (): array => ['theme:validation_corrected', 'theme:event']);
        }
    }
}
