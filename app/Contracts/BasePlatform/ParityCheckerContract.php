<?php

declare(strict_types=1);

namespace App\Contracts\BasePlatform;

use App\Data\ParityReportData;

interface ParityCheckerContract
{
    /**
     * @param  array<int, string>  $profiles
     * @return array<int, ParityReportData>
     */
    public function run(array $profiles): array;
}
