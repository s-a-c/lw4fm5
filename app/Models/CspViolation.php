<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CspViolationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class CspViolation extends Model
{
    /** @use HasFactory<CspViolationFactory> */
    use HasFactory;
}
