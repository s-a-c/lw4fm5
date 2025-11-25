<?php

declare(strict_types=1);

namespace App\Enums;

enum ValidationStatus: string
{
    case Pass = 'pass';
    case Warning = 'warning';
    case Fail = 'fail';
}
