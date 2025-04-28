<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::name('auth.')->group(function () {
    include 'auth.php';
});

Route::get('/',[IndexController::class,'home'])
    ->name('home');

