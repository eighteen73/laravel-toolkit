<?php

namespace Eighteen73\AI\Tests;

use Eighteen73\AI\AIAssistServiceProvider;
use Laravel\Boost\BoostServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BoostServiceProvider::class,
            AIAssistServiceProvider::class,
        ];
    }
}
