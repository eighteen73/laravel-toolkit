<?php

namespace Eighteen73\Ai\Tests;

use Eighteen73\Ai\AiToolkitServiceProvider;
use Laravel\Boost\BoostServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BoostServiceProvider::class,
            AiToolkitServiceProvider::class,
        ];
    }
}
