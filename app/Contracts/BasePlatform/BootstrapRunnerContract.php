<?php

declare(strict_types=1);

namespace App\Contracts\BasePlatform;

use App\Services\BasePlatform\BootstrapRun;

interface BootstrapRunnerContract
{
    public function run(string $profile, bool $forceClean): BootstrapRun;
}
