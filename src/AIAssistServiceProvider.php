<?php

namespace Eighteen73\Ai;

use Eighteen73\Ai\Boost\Install\Agents\Gemini;
use Illuminate\Support\ServiceProvider;
use Laravel\Boost\Boost;

class AiToolkitServiceProvider extends ServiceProvider
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
