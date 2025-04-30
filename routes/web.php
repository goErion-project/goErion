<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

// Theme routes
Route::get('/theme/dark', function () {
    return redirect()->back()->withCookie(cookie()->forever('theme', 'dark'));
})->name('theme.dark');

Route::get('/theme/light', function () {
    return redirect()->back()->withCookie(cookie()->forever('theme', 'light'));
})->name('theme.light');

// Main routes
Route::name('auth.')->group(function () {
    include 'auth.php';
});

//admin routes
Route::prefix('admin')->group(function ()
{
    Route::middleware(['admin_panel_access'])->group(function ()
    {
        include 'admin.php';
    });
});


// Profile routes
Route::middleware(['auth'])->group(function () {
    include 'profile.php';
});

// Home route
Route::get('/', [IndexController::class, 'home'])
    ->name('home');
