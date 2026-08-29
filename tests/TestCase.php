<?php

namespace Eighteen73\Toolkit\Tests;

use Eighteen73\Toolkit\ToolkitServiceProvider;
use Laravel\Boost\BoostServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BoostServiceProvider::class,
            ToolkitServiceProvider::class,
        ];
    }
}
