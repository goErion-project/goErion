<?php

namespace App\Providers;

use App\Models\Category;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\ElasticsearchEngine;
use Laravel\Scout\EngineManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        try {
        $categories = Category::with('children')
        ->withCount('products')
        ->whereNull('parent_id')
        ->get();
        View::share('categories', $categories);
        } catch (\Exception $e) {
            \Log::error('Error fetching categories: ' . $e->getMessage());
            view()->share('categories', collect());
        }
        resolve(EngineManager::class)->extend('elastic', function () {
            return new ElasticsearchEngine();
        });
    }
}
