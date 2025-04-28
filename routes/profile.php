<?php


use App\Http\Controllers\ProfileController;

Route::prefix('profile')->group(function ()
{
    Route::get('index',[ProfileController::class,'index'])
        ->name('profile.index');
});
