<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::middleware(['guest'])->group(function () {
    Route::get('signin', [LoginController::class,'showSignIn'])
        ->name('signin');
    Route::post('signin', [LoginController::class,'postSignIn'])
        ->name('signin.post');

    Route::get('signup/{refid?}', [RegisterController::class,'showSignUp'])
        ->name('signup');
    Route::post('signup', [RegisterController::class,'signUpPost'])
        ->name('signup.post');
});

Route::post('signout', [LoginController::class,'postSignout'])
    ->name('signout.post');
