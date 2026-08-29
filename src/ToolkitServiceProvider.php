<?php

namespace Eighteen73\Toolkit;

use Eighteen73\Toolkit\Boost\Install\Agents\Gemini;
use Illuminate\Support\ServiceProvider;
use Laravel\Boost\Boost;

class ToolkitServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (class_exists(Boost::class)) {
            Boost::registerAgent('eighteen73-gemini', Gemini::class);
        }
    }
}
