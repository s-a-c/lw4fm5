<?php

declare(strict_types=1);

namespace App\Enums;

enum RiskLevel: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
}
