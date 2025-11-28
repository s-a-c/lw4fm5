<?php

declare(strict_types=1);

namespace App\Enums;

enum ThemeFlavor: string
{
    // Catppuccin
    case Latte = 'latte';
    case Frappe = 'frappe';
    case Macchiato = 'macchiato';
    case Mocha = 'mocha';

    // Kanagawa
    case Wave = 'wave';
    case Dragon = 'dragon';
    case Lotus = 'lotus';

    // Tokyo Night
    case Night = 'night';
    case Day = 'day';

    // Gruvbox
    case Dark = 'dark';
    case Light = 'light';

    // Default (for single-flavor themes)
    case Default = 'default';

    public function label(): string
    {
        return match ($this) {
            self::Latte => 'Latte (Light)',
            self::Frappe => 'Frappé',
            self::Macchiato => 'Macchiato',
            self::Mocha => 'Mocha',
            self::Wave => 'Wave (Default)',
            self::Dragon => 'Dragon (Deep)',
            self::Lotus => 'Lotus (Light)',
            self::Night => 'Night',
            self::Day => 'Day',
            self::Dark => 'Dark',
            self::Light => 'Light',
            self::Default => 'Default',
        };
    }

    public function isLight(): bool
    {
        return in_array($this, [self::Latte, self::Lotus, self::Day, self::Light], true);
    }
}
