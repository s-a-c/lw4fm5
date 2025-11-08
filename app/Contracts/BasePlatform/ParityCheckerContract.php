<?php

declare(strict_types=1);

namespace App\Contracts\BasePlatform;

use App\Services\BasePlatform\ParityReport;

interface ParityCheckerContract
{
    /**
     * @param  array<int, string>  $profiles
     * @return array<int, ParityReport>
     */
    public function run(array $profiles): array;
}
