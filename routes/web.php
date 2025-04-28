<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::name('auth.')->group(function () {
    include 'auth.php';
});

//profile routes
Route::middleware(['auth'])->group(function ()
{
    include 'profile.php';
});

Route::get('/',[IndexController::class,'home'])
    ->name('home');

