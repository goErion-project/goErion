<?php

namespace Modules\FeaturedProducts\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\FeaturedProducts\Providers\RouteServiceProvider;
use Modules\FeaturedProducts\main\FeaturedStatus;

class FeaturedProductsServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(__DIR__ . '/../database/Migrations');
        $this->registerFeaturedStatus();
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
            __DIR__.'/../config/config.php' => config_path('featuredproducts.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'featuredproducts'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/featuredproducts');

        $sourcePath = __DIR__.'/../resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/featuredproducts';
        }, \Config::get('view.paths')), [$sourcePath]), 'featuredproducts');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/featuredproducts');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'featuredproducts');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../resources/lang', 'featuredproducts');
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

    public function registerFeaturedStatus(): void
    {
        $this->app->bind('Modules\FeaturedProducts\main\FeaturedStatus', function ($app) {
            return new FeaturedStatus();
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
