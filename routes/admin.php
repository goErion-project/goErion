<?php

use App\Http\Controllers\Admin\AdminController;

Route::get('index',[AdminController::class,'index'])
    ->name('admin.index');;
