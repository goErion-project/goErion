<?php

use Illuminate\Support\Facades\Route;
use Modules\FinalizeEarly\Http\Controllers\FinalizeEarlyController;

Route::middleware('auth:api')->get('/finalizeearly', function (Request $request) {
    return $request->user();
});
