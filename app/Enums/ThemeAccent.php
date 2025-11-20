<?php

declare(strict_types=1);

namespace App\Enums;

enum ThemeAccent: string
{
    case Primary = 'primary';
    case Blue = 'blue';
    case Red = 'red';
    case Green = 'green';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
