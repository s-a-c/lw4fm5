<?php

declare(strict_types=1);

namespace App\Enums;

enum Theme: string
{
    case Catppuccin = 'catppuccin';
    case Kanagawa = 'kanagawa';

    public function label(): string
    {
        return match ($this) {
            self::Catppuccin => 'Catppuccin',
            self::Kanagawa => 'Kanagawa',
        };
    }

    /**
     * @return array<int, ThemeFlavor>
     */
    public function flavors(): array
    {
        return match ($this) {
            self::Catppuccin => [
                ThemeFlavor::Latte,
                ThemeFlavor::Frappe,
                ThemeFlavor::Macchiato,
                ThemeFlavor::Mocha,
            ],
            self::Kanagawa => [
                ThemeFlavor::Wave,
                ThemeFlavor::Dragon,
                ThemeFlavor::Lotus,
            ],
        };
    }
}
