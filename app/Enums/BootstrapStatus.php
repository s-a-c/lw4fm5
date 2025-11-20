<?php

declare(strict_types=1);

namespace App\Enums;

enum BootstrapStatus: string
{
    case Success = 'success';
    case Warning = 'warning';
    case Failed = 'failed';
}
