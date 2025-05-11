<?php


use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\BitmessageController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorController;



/**
 * All Routes related to user profile
 * Grouped under the prefix "profile" and under auth middleware
 */
Route::prefix('profile')->group(function ()
{
    Route::get('index',[ProfileController::class,'index'])
        ->name('profile.index');
    Route::post('changepassword', [ProfileController::class,'changePassword'])
        -> name('profile.password.change'); // change password route
    Route::get('2fa/{turn}', [ProfileController::class,'change2fa'])
        -> name('profile.2fa.change'); // change 2fa

    // add or remove to whishlist
    Route::get('add/wishlist/{product}', [ProfileController::class,'addRemoveWishlist'])
        -> name('profile.wishlist.add');
    Route::get('wishlist', [ProfileController::class,'wishlist'])
        -> name('profile.wishlist');

    //PGP Routes
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


    //vendor Routes
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

    // Sales Routes
    Route::get('sales/{state?}', [VendorController::class,'sales'])
        -> name('profile.sales');
    Route::get('sale/{sale}', [VendorController::class,'sale'])
        -> name('profile.sales.single');
    Route::get('sales/{sale}/sent/confirm', [VendorController::class,'confirmSent'])
        -> name('profile.sales.sent.confirm');
    Route::get('sale/{sale}/sent', [VendorController::class,'markAsSent'])
        -> name('profile.sales.sent');

    // Cart Routes
    Route::get('cart', [ProfileController::class,'cart'])
        -> name('profile.cart');
    Route::post('cart/{product}/add', [ProfileController::class,'addToCart'])
        -> name('profile.cart.add');
    Route::get('cart/clear', [ProfileController::class,'clearCart'])
        -> name('profile.cart.clear');
    Route::get('cart/remove/{product}', [ProfileController::class,'removeProduct'])
        -> name('profile.cart.remove');
    Route::get('checkout', [ProfileController::class,'checkout'])
        -> name('profile.cart.checkout');
    Route::get('make/purchase', [ProfileController::class,'makePurchases'])
        -> name('profile.cart.make.purchases');

    // Purchases Routes
    Route::get('purchases/{state?}', [ProfileController::class,'purchases'])
        -> name('profile.purchases');
    Route::get('purchases/{purchase}/message', [ProfileController::class,'purchaseMessage'])
        -> name('profile.purchases.message');
    Route::get('purchase/{purchase}', [ProfileController::class,'purchase'])
        -> name('profile.purchases.single');
    Route::get('purchase/{purchase}/delivered/confirm', [ProfileController::class,'deliveredConfirm'])
        -> name('profile.purchases.delivered.confirm');
    Route::get('purchase/{purchase}/delivered', [ProfileController::class,'markAsDelivered'])
        -> name('profile.purchases.delivered');

    // canceled for both Vendor and Buyer
    Route::get('purchase/{purchase}/canceled/confirm', [ProfileController::class,'confirmCanceled'])
        -> name('profile.purchases.canceled.confirm');
    Route::get('purchase/{purchase}/canceled', [ProfileController::class,'markAsCanceled'])
        -> name('profile.purchases.canceled');

    // Purchase - Disputes
    Route::post('purchase/{purchase}/dispute', [ProfileController::class,'makeDispute'])
        -> name('profile.purchases.dispute');
    Route::post('purchase/dispute/{dispute}/new/message', [ProfileController::class,'newDisputeMessage'])
        -> name('profile.purchases.disputes.message');
    Route::post('purchase/{purchase}/dispute/resolve', [AdminController::class,'resolveDispute'])
        -> name('profile.purchases.disputes.resolve');

    // Purchase - Feedbacks
    Route::post('purchase/{purchase}/feedback/new', [ProfileController::class,'leaveFeedback'])
        -> name('profile.purchases.feedback.new');

    /**
     * Messages
     */
    Route::middleware(['can_read_messages'])->group(function () {
        Route::get('messages/{conversation?}', [MessageController::class,'messages'])
            -> name('profile.messages');
        Route::post('messages/conversation/new', [MessageController::class,'startConversation'])
            -> name('profile.messages.conversation.new');
        Route::get('messages/conversations/list', [MessageController::class,'listConversations'])
            -> name('profile.messages.conversations.list');
        Route::post('messages/{conversation}/message/new', [MessageController::class,'newMessage'])
            -> name('profile.messages.message.new');
        Route::get('messages/{conversation}/sendmessage', [MessageController::class,'newMessage'])
            -> name('profile.messages.send.message'); // get a request for redirecting from a new conversation
    });
    Route::get('messages/decrypt/key',[MessageController::class,'decryptKeyShow'])
        ->name('profile.messages.decrypt.show');
    Route::post('messages/decrypt/key',[MessageController::class,'decryptKeyPost'])
        ->name('profile.messages.decrypt.post');

    /**
     * Notifications
     */

    Route::get('notifications',[NotificationController::class,'viewNotifications'])
        ->name('profile.notifications');
    Route::post('notifications/delete',[NotificationController::class,'deleteNotifications'])
        ->name('profile.notifications.delete');

    /**
     * Bitmessage
     */
    Route::get('bitmessage',[BitmessageController::class,'show'])
        ->name('profile.bitmessage');
    Route::post('bitmessage/send/code',[BitmessageController::class,'sendConfirmation'])
        ->name('profile.bitmessage.sendcode');
    Route::post('bitmessage/confirm/code',[BitmessageController::class,'confirmAddress'])
        ->name('profile.bitmessage.confirmcode');

    /**
     * Tickets
     */
    Route::get('tickets/{ticket?}', [ProfileController::class,'tickets'])
        -> name('profile.tickets');
    Route::post('tickets/new', [ProfileController::class,'newTicket'])
        -> name('profile.tickets.new');
    Route::post('tickets/{ticket}/newmessage', [ProfileController::class,'newTicketMessage'])
        -> name('profile.tickets.message.new');


    /**
     * Product clone
     */
    Route::get('product/clone/{product}',[ProductController::class,'cloneProductShow'])
        ->name('profile.vendor.product.clone.show');
    Route::post('product/clone/{product}',[ProductController::class,'cloneProductPost'])
        ->name('profile.vendor.product.clone.post');


});
