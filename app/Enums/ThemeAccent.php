<?php

declare(strict_types=1);

namespace App\Enums;

enum ThemeAccent: string
{
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
    case Success = 'success';

    public function label(): string
    {
        return match ($this) {
            self::Primary => 'Primary',
            self::Secondary => 'Secondary',
            self::Info => 'Info',
            self::Warning => 'Warning',
            self::Error => 'Error',
            self::Success => 'Success',
        };
    }
}
