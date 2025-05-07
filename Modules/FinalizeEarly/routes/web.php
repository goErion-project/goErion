<?php

use Illuminate\Support\Facades\Route;
use Modules\FinalizeEarly\Http\Controllers\FinalizeEarlyController;

Route::prefix('finalizeearly')->group(function() {
    Route::get('/', [FinalizeEarlyController::class,'index']);
});
