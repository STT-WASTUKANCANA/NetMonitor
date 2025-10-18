<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class DevelopmentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (App::environment('local')) {
            $this->app['router']->pushMiddlewareToGroup('web', \App\Http\Middleware\LiveReloadMiddleware::class);
        }
    }
}