<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use RuntimeException;

final class UnsupportedProfileException extends RuntimeException
{
    public function __construct(string $profile)
    {
        parent::__construct(sprintf('Unsupported profile [%s]', $profile));
    }
}
