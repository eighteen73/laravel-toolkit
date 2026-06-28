<?php

namespace Eighteen73\AI;

use Eighteen73\AI\Boost\Install\Agents\Gemini;
use Illuminate\Support\ServiceProvider;
use Laravel\Boost\Boost;

class AIAssistServiceProvider extends ServiceProvider
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
        Boost::registerAgent('gemini', Gemini::class);
    }
}
