<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
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

    Route::get('mnemonic/show',[RegisterController::class,'showMnemonic'])
        ->name('mnemonic');

    Route::get('/forgotpassword', [ForgotPasswordController::class,'showForget']);
    Route::get('/forgotpassowrd/mnemonic', [ForgotPasswordController::class,'showMnemonic']);
    Route::get('/forgotpassword/pgp', [ForgotPasswordController::class,'showPGP']);

    Route::post('/forgotpassword/mnemonic', [ForgotPasswordController::class,'resetMnemonic']);
    Route::post('/forgotpassword/pgp', [ForgotPasswordController::class,'sendVerify']);

    Route::get('/forgotpassword/pgp/verify', [ForgotPasswordController::class,'showVerify'])
        ->name('pgprecover');
    Route::post('/forgotpassword/pgp/verify', [ForgotPasswordController::class,'resetPgp'])
        ->name('resetpgp');
});

Route::get('verify', [LoginController::class,'showVerify'])
    -> name('verify');
Route::post('verify', [LoginController::class,'postVerify'])
    -> name('verify.post');


Route::post('signout', [LoginController::class,'postSignout'])
    ->name('signout.post');
