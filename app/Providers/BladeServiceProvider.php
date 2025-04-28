<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     * Bootstrap Blade Engine Services
     */
    public function boot(): void
    {
        Blade::if('error', function ($name,$errors) {
            return $errors->has($name);
        });
        Blade::if('isroute', function ($routeName) {
            return str_contains(\Route::currentRouteName(), $routeName) !== false;
        });
    }
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
}
