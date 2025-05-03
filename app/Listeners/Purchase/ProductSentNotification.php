<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\ProductSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductSentNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProductSent  $event
     * @return void
     */
    public function handle(ProductSent $event): void
    {

        $content = 'Your product has been sent by vendor ['.$event->vendor->user->username.']';
        $routeName = 'profile.purchases.single';
        $routeParams = serialize(['purchase'=>$event->purchase->id]);
        $event->buyer->notify($content,$routeName,$routeParams);
    }
}
