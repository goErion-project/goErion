<?php

namespace App\Listeners\Experience;

use App\Events\Purchase\ProductDelivered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductDeliveredXPUpdate
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
     * @param  ProductDelivered  $event
     * @return void
     */
    public function handle(ProductDelivered $event): void
    {
        $multiplier = config('experience.multipliers.product_delivered');
        $amount = round($event->purchase->getSumDollars()*$multiplier,0);
        $event->vendor->grantExperience($amount);
    }
}
