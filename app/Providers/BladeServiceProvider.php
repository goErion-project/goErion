<?php

namespace App\Providers;

use App\Marketplace\ModuleManager;
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
        Blade::if('vendor', function () {
            return auth() ->check() && auth() ->user()->isVendor();
        });

        Blade::if('admin', function () {
            return auth() ->check() && auth() ->user()->isAdmin();
        });

        Blade::if('isModuleEnabled', function ($moduleName) {
            return ModuleManager::isEnabled($moduleName);
        });

        Blade::if('search', function ()
        {
            $display = false;
            $routes =
                [
                    'home',
                    'category',
                ];
            foreach ($routes as $route)
            {
                if (str_contains(\Route::currentRouteName(), $route) !== false)
                {
                    $display = true;
                    break;
                }
            }
            return $display;
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
