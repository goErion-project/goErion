<?php

use Illuminate\Support\Facades\Route;
use Modules\FeaturedProducts\Http\Controllers\FeaturedProductsController;

Route::middleware('auth:api')->get('/featuredproducts', function (Request $request) {
    return $request->user();
});
