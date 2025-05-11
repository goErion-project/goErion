<?php

namespace Modules\FinalizeEarly\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\FinalizeEarly\main\Info;
use Modules\FinalizeEarly\main\Procedure;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FinalizeEarlyServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->registerInfo();
        $this->registerProcedure();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('finalizeearly.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'finalizeearly'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/finalizeearly');

        $sourcePath = __DIR__.'/../resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/finalizeearly';
        }, \Config::get('view.paths')), [$sourcePath]), 'finalizeearly');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/finalizeearly');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'finalizeearly');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../resources/lang', 'finalizeearly');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories(): void
    {
//        if (! app()->environment('production')) {
//            app(Factory::class)->load(__DIR__ . '/../../database/factories');
//        }
    }
    public function registerInfo(): void
    {
        $this->app->bind('FinalizeEarlyModule\main\Info', function ($app) {
            return new Info();
        });
    }
    public function registerProcedure(): void
    {
        $this->app->bind('FinalizeEarlyModule\main\Procedure', function ($app) {
            return new Procedure();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }
}
