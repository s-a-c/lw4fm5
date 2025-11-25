<?php

declare(strict_types=1);

namespace App\Enums;

enum ReviewCadence: string
{
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
}
