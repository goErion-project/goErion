<?php


use App\Http\Controllers\ProductController;
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

    Route::post('vendor/address', [ProfileController::class,'changeAddress'])
        -> name('profile.vendor.address'); // add address to account
    Route::get('vendor/address/remove/{id}', [ProfileController::class,'removeAddress'])
        -> name('profile.vendor.address.remove'); // add address to account


    //vendor routes
    Route::get('vendor',[VendorController::class,'vendor'])
        ->name('profile.vendor');
    Route::post('vendor/update/profile',[VendorController::class,'updateVendorProfilePost'])
        ->name('profile.vendor.update.post');

    // Digital options
    Route::get('vendor/product/digital/add', [VendorController::class,'addDigitalShow'])
        -> name('profile.vendor.product.digital');
    Route::post('vendor/product/digital/add/{product?}', [VendorController::class,'addDigital'])
        -> name('profile.vendor.product.digital.post');

    // Product adds basic info
    Route::get('vendor/product/add/{type?}', [VendorController::class,'addBasicShow'])
        -> name('profile.vendor.product.add');
    Route::post('vendor/product/adding/{product?}', [VendorController::class,'addShow'])
        -> name('profile.vendor.product.add.post');

    // Add remove offers
    Route::get('vendor/product/offers/add', [VendorController::class,'addOffersShow'])
        -> name('profile.vendor.product.offers');
    Route::post('vendor/product/offers/new/{product?}', [VendorController::class,'addOffer'])
        -> name('profile.vendor.product.offers.add'); // add offer
    Route::get('vendor/product/offers/remove/{quantity}/{product?}', [VendorController::class,'removeOffer'])
        -> name('profile.vendor.product.offers.remove'); // add offer

    // Delivery
    Route::get('vendor/product/delivery/add', [VendorController::class,'addDeliveryShow'])
        -> name('profile.vendor.product.delivery');
    Route::post('vendor/product/delivery/add/{product?}', [VendorController::class,'newShipping'])
        -> name('profile.vendor.product.delivery.new');
    Route::post('vendor/product/delivery/options/{product?}', [VendorController::class,'newShippingOption'])
        -> name('profile.vendor.product.delivery.options');
    Route::get('vendor/product/delivery/remove/{index}/{product?}', [VendorController::class,'removeShipping'])
        -> name('profile.vendor.product.delivery.remove');

    // Images section
    Route::get('vendor/product/images/add', [VendorController::class,'addImagesShow'])
        -> name('profile.vendor.product.images');
    Route::get('vendor/product/images/remove/{id}/{product?}', [VendorController::class,'removeImage'])
        -> name('profile.vendor.product.images.remove');
    Route::get('vendor/product/images/default/{id}/{product?}', [VendorController::class,'defaultImage'])
        -> name('profile.vendor.product.images.default');
    Route::post('vendor/product/images/add/{product?}', [VendorController::class,'addImage'])
        -> name('profile.vendor.product.images.post'); // new image

    // New product
    Route::post('vendor/product/post', [VendorController::class,'newProduct'])
        -> name('profile.vendor.product.post');

    // Delete product
    Route::get('vendor/product/{id}/delete/confirmation', [VendorController::class,'confirmProductRemove'])
        -> name('profile.vendor.product.remove.confirm');
    Route::get('vendor/product/{id}/delete', [VendorController::class,'removeProduct'])
        -> name('profile.vendor.product.remove');

    // Edit Product
    Route::get('vendor/product/edit/{id}/section/{section?}', [VendorController::class,'editProduct'])
        -> name('profile.vendor.product.edit');

    /**
     * Product clone
     */
    Route::get('product/clone/{product}',[ProductController::class,'cloneProductShow'])
        ->name('profile.vendor.product.clone.show');
    Route::post('product/clone/{product}',[ProductController::class,'cloneProductPost'])
        ->name('profile.vendor.product.clone.post');


});
