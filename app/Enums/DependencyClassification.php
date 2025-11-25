<?php

declare(strict_types=1);

namespace App\Enums;

enum DependencyClassification: string
{
    case Core = 'core';
    case Optional = 'optional';
    case Experimental = 'experimental';
}
