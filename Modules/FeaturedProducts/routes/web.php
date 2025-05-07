<?php

use Illuminate\Support\Facades\Route;
use Modules\FeaturedProducts\Http\Controllers\FeaturedProductsController;


Route::prefix('featuredproducts')->group(function () {
    Route::get('/', [FeaturedProductsController::class, 'index']);
});
