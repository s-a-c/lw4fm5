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
        };
    }

    public function isLight(): bool
    {
        return in_array($this, [self::Latte, self::Lotus], true);
    }
}
