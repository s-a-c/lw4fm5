<?php

declare(strict_types=1);

namespace App\Support;

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use Filament\Support\Colors\Color;

final class ThemeColorHelper
{
    /**
     * @return array<string, array<int|string, int|string>|string>
     */
    public static function getFilamentColors(UserSettingsData $settings): array
    {
        // Resolve the specific hex code for the selected accent/theme combo
        $primaryHex = self::resolveAccentHex($settings);

        /** @var array<string, array<int|string, int|string>|string> */
        return [
            'primary' => Color::hex($primaryHex),
            // You can implement custom logic for danger/success per theme here if desired
        ];
    }

    private static function resolveAccentHex(UserSettingsData $settings): string
    {
        // If "Primary" is selected, return the theme's brand color
        if ($settings->accent === ThemeAccent::Primary) {
            return match ($settings->theme) {
                Theme::Catppuccin => '#cba6f7', // Mauve
                Theme::Kanagawa => '#7e9cd8',   // Spring Blue
            };
        }

        // Otherwise return the specific color requested
        return match ($settings->accent) {
            ThemeAccent::Blue => match ($settings->theme) {
                Theme::Kanagawa => '#7fb4ca', // Crystal Blue
                default => '#89b4fa', // Catppuccin Blue
            },
            ThemeAccent::Red => match ($settings->theme) {
                Theme::Kanagawa => '#c34043', // Autumn Red
                default => '#f38ba8', // Catppuccin Red
            },
            ThemeAccent::Green => match ($settings->theme) {
                Theme::Kanagawa => '#76946a', // Autumn Green
                default => '#a6e3a1', // Catppuccin Green
            },
        };
    }
}
