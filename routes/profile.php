<?php


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorController;

Route::prefix('profile')->group(function ()
{
    Route::get('index',[ProfileController::class,'index'])
        ->name('profile.index');

    //PGP routes
    Route::get('pgp',[ProfileController::class,'pgp'])
        ->name('profile.pgp');
    Route::post('pgp',[ProfileController::class,'pgpPost'])
        ->name('profile.pgp.post');
    Route::get('pgp/confirm',[ProfileController::class,'pgpConfirm'])
        ->name('profile.pgp.confirm');
    Route::post('pgp/confirm',[ProfileController::class,'storePGP'])
        ->name('profile.pgp.store');
    Route::get('pgp/old',[ProfileController::class,'oldpgp'])
        ->name('profile.pgp.old');

    Route::get('become/vendor',[ProfileController::class,'becomeVendor'])
        ->name('profile.vendor.become');
    Route::get('becomer',[ProfileController::class,'become'])
        ->name('profile.become');

    //vendor routes
    Route::get('vendor',[VendorController::class,'vendor'])
        ->name('profile.vendor');
    Route::post('vendor/update/profile',[VendorController::class,'updateVendorProfilePost'])
        ->name('profile.vendor.update.post');
});
