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
     * All themes offer 6 accent options: Primary, Secondary, Info, Warning, Error, and Success.
     *
     * @return array<int, ThemeAccent>
     */
    public function getAvailableAccents(Theme $theme): array
    {
        // All themes (including Default) offer all 6 accent options
        return [
            ThemeAccent::Primary,
            ThemeAccent::Secondary,
            ThemeAccent::Info,
            ThemeAccent::Warning,
            ThemeAccent::Error,
            ThemeAccent::Success,
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
        // Default mapping: All accents use shade 500
        // This can be customized per theme/flavor/accent combination
        return match ($accent) {
            ThemeAccent::Primary => 500,
            ThemeAccent::Secondary => 500,
            ThemeAccent::Info => 500,
            ThemeAccent::Warning => 500,
            ThemeAccent::Error => 500,
            ThemeAccent::Success => 500,
        };
    }
}
