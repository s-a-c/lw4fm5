<?php

declare(strict_types=1);

namespace App\Enums;

enum ThemeFlavor: string
{
    // Generic
    case Dark = 'dark';
    case Light = 'light';
    case System = 'system';

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
    case Storm = 'storm';
    case Moon = 'moon'; // Also used in Rose Pine

    // Rose Pine (Moon is above)
    case Dawn = 'dawn';

    // Ayu
    case Mirage = 'mirage';

    // Monokai
    case Classic = 'classic';

    // Oceanic
    case Next = 'next';

    // Synthwave
    case EightyFour = '84'; // Maps to data-flavor="84"

    // Cyberpunk
    case Neon = 'neon';

    public function label(): string
    {
        return match ($this) {
            self::Dark => 'Dark',
            self::Light => 'Light',
            self::System => 'System',
            self::Latte => 'Latte (Light)',
            self::Frappe => 'Frappé',
            self::Macchiato => 'Macchiato',
            self::Mocha => 'Mocha',
            self::Wave => 'Wave (Default)',
            self::Dragon => 'Dragon (Deep)',
            self::Lotus => 'Lotus (Light)',
            self::Night => 'Night',
            self::Storm => 'Storm',
            self::Moon => 'Moon',
            self::Dawn => 'Dawn (Light)',
            self::Mirage => 'Mirage',
            self::Classic => 'Classic',
            self::Next => 'Next',
            self::EightyFour => '84',
            self::Neon => 'Neon',
        };
    }

    public function isLight(): bool
    {
        return in_array($this, [
            self::Light,
            self::Latte,
            self::Lotus,
            self::Dawn,
            self::Classic, // Monokai Classic is dark, removed from here
        ], true);
    }
}
