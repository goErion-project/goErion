<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BitmessageController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;

Route::get('index',[AdminController::class,'index'])
    ->name('admin.index');

// Category routes
Route::get('categories', [AdminController::class,'categories'])
    -> name('admin.categories');
Route::post('category/new', [AdminController::class,'newCategory'])
    -> name('admin.categories.new');
Route::get('category/delete/{id}', [AdminController::class,'removeCategory'])
    -> name('admin.categories.delete');
Route::get('category/{id}', [AdminController::class,'editCategoryShow'])
    -> name('admin.categories.show');
Route::post('category/{id}', [AdminController::class,'editCategory'])
    -> name('admin.categories.edit');

// Mass message routes
Route::get('message', [AdminController::class,'massMessage'])
    -> name('admin.messages.mass');
Route::post('message/send', [AdminController::class,'sendMessage'])
    -> name('admin.messages.send');

// User routes
Route::get('users',[UserController::class,'users'])
    ->name('admin.users');
Route::post('users/query',[UserController::class,'usersPost'])
    ->name('admin.users.query');
Route::get('users/{user?}',[UserController::class,'userView'])
    ->name('admin.users.view');

Route::post('users/edit/group/{user}',[UserController::class,'editUserGroup'])
    ->name('admin.user.edit.group');
Route::post('users/edit/info/{user}',[UserController::class,'editBasicInfo'])
    ->name('admin.user.edit.info');

Route::post('users/ban/{user}', [UserController::class,'banUser'])
    ->name('admin.user.ban');
Route::get('users/remove/ban/{ban}', [UserController::class,'removeBan'])
    ->name('admin.ban.remove');


// Log
Route::get('log',[LogController::class,'showLog'])
    ->name('admin.log');

// Products
Route::get('products',[ProductController::class,'products'])
    ->name('admin.products');
Route::post('products/query',[ProductController::class,'productsPost'])
    ->name('admin.products.query');
Route::post('product/delete',[ProductController::class,'deleteProduct'])
    ->name('admin.product.delete');
Route::get('product/{id}/{section?}', [ProductController::class,'editProduct'])
    -> name('admin.product.edit');

Route::get('purchases', [ProductController::class,'purchases'])
    ->name('admin.purchases');


// Bitmessage
Route::get('bitmessage',[BitmessageController::class,'show'])
->name('admin.bitmessage');

// Disputes
Route::get('disputes', [AdminController::class,'disputes'])
    -> name('admin.disputes');
Route::get('purchase/{purchase}', [AdminController::class,'purchase'])
    -> name('admin.purchase');

// Support tickets
Route::get('tickets', [AdminController::class,'tickets'])
    -> name('admin.tickets');
Route::get('ticket/{ticket}', [AdminController::class,'viewTicket'])
    -> name('admin.tickets.view');
Route::get('ticket/{ticket}/solve', [AdminController::class,'solveTicket'])
    -> name('admin.tickets.solve');

// Vendor purchases
Route::get('vendor/purchases', [AdminController::class,'vendorPurchases'])
    -> name('admin.vendor.purchases');

// Featured products

Route::get('products/featured',[ProductController::class,'featuredProductsShow'])
    ->name('admin.featuredproducts.show');
Route::get('products/featured/mark/{product}',[ProductController::class,'markAsFeatured'])
    ->name('admin.product.markfeatured');

Route::post('products/featured/remove',[ProductController::class,'removeFromFeatured'])
    ->name('admin.featuredproducts.remove');

// Remove tickets

Route::post('tickets/remove',[AdminController::class,'removeTickets'])
    ->name('admin.tickets.remove');
