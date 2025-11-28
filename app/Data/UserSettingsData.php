<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Services\Theme\ThemeService;
use App\Support\ThemeErrorLogger;
use Spatie\LaravelData\Data;
use Throwable;

final class UserSettingsData extends Data
{
    public function __construct(
        public Theme $theme = Theme::Catppuccin,
        public ThemeFlavor $flavor = ThemeFlavor::Mocha,
        public ThemeAccent $accent = ThemeAccent::Primary,
    ) {}

    /**
     * Create a UserSettingsData instance from array/JSON, handling invalid enum values.
     *
     * Catches invalid enum values during deserialization and triggers validation/correction
     * via ThemeService, including theme-specific accent validation via ThemeAccentMapper.
     */
    public static function from(mixed ...$payloads): static
    {
        try {
            return parent::from(...$payloads);
        } catch (Throwable $e) {
            // Invalid enum values or corrupted data - log and return defaults
            // Redact potentially sensitive payload data (only log structure, not content)
            $payloadStructure = is_array($payloads[0] ?? null)
                ? array_keys($payloads[0])
                : ['type' => gettype($payloads[0] ?? null)];

            ThemeErrorLogger::warning(
                'Invalid theme value detected during deserialization',
                $e,
                [
                    'payload_structure' => $payloadStructure,
                    'payload_count' => count($payloads),
                    'event_type' => 'deserialization_error',
                ]
            );

            // Return default instance
            $default = new self();

            // If first payload is an array with some valid values, try to preserve them
            $payload = $payloads[0] ?? null;
            if (is_array($payload)) {
                $themeValue = $payload['theme'] ?? null;
                $flavorValue = $payload['flavor'] ?? null;
                $accentValue = $payload['accent'] ?? null;

                $theme = is_string($themeValue) || is_int($themeValue) ? Theme::tryFrom($themeValue) : null;
                $flavor = is_string($flavorValue) || is_int($flavorValue) ? ThemeFlavor::tryFrom($flavorValue) : null;
                $accent = is_string($accentValue) || is_int($accentValue) ? ThemeAccent::tryFrom($accentValue) : null;

                if ($theme !== null) {
                    $default->theme = $theme;
                }
                if ($flavor !== null) {
                    $default->flavor = $flavor;
                }
                if ($accent !== null) {
                    $default->accent = $accent;
                }

                // Validate and correct via ThemeService
                $themeService = app(ThemeService::class);
                $validated = $themeService->resolveThemeData($default);

                return new self(
                    theme: $validated->theme,
                    flavor: $validated->flavor,
                    accent: $validated->accent,
                );
            }

            return $default;
        }
    }
}
