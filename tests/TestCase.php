<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\RequiresEnvironmentVariable;
use PHPUnit\Framework\Attributes\Timeout;

#[RequiresEnvironmentVariable('APP_BASE_PATH')]
abstract class TestCase extends BaseTestCase
{
    //
}
