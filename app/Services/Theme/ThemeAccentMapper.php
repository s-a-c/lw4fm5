<?php

declare(strict_types=1);

namespace App\Services\Theme;

use App\Contracts\ThemeAccentMapperInterface;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;

final class ThemeAccentMapper implements ThemeAccentMapperInterface
{
    /**
     * Get available accent colors for a given theme.
     *
     * All themes offer 4 accent options: Primary (Theme), Blue, Red, and Green.
     *
     * @return array<int, ThemeAccent>
     */
    public function getAvailableAccents(Theme $theme): array
    {
        // None theme has no accents (system default)
        if ($theme === Theme::None) {
            return [];
        }

        // All other themes offer all 4 accent options
        return [
            ThemeAccent::Primary,
            ThemeAccent::Blue,
            ThemeAccent::Red,
            ThemeAccent::Green,
        ];
    }

    /**
     * Validate if an accent color is valid for a given theme.
     */
    public function validateAccent(Theme $theme, ThemeAccent $accent): bool
    {
        $availableAccents = $this->getAvailableAccents($theme);

        return in_array($accent, $availableAccents, true);
    }

    /**
     * Get Flux CSS variable name for accent color.
     *
     * Format: --accent-flux-zinc-{shade}
     */
    public function getFluxVariableName(Theme $theme, ThemeFlavor $flavor, ThemeAccent $accent): string
    {
        $shade = $this->getAccentShade($accent);

        return "--accent-flux-zinc-{$shade}";
    }

    /**
     * Get Filament CSS variable name for accent color.
     *
     * Format: --accent-filament-gray-{shade}
     */
    public function getFilamentVariableName(Theme $theme, ThemeFlavor $flavor, ThemeAccent $accent): string
    {
        $shade = $this->getAccentShade($accent);

        return "--accent-filament-gray-{$shade}";
    }

    /**
     * Get the shade number for an accent color based on theme, flavor, and accent.
     *
     * This is a simplified mapping. In a full implementation, this would
     * map to actual color values from the theme's color palette.
     */
    private function getAccentShade(ThemeAccent $accent): int
    {
        // Default mapping: Primary = 500, Blue = 500, Red = 500, Green = 500
        // This can be customized per theme/flavor/accent combination
        return match ($accent) {
            ThemeAccent::Primary => 500,
            ThemeAccent::Blue => 500,
            ThemeAccent::Red => 500,
            ThemeAccent::Green => 500,
        };
    }
}
