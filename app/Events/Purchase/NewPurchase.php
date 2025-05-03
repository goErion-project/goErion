<?php

namespace App\Events\Purchase;

use App\Models\Purchase;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPurchase
{
    use Dispatchable;


    /**
     * User who purchased the product
     *
     * @var mixed
     */
    public mixed $buyer;

    /**
     * User who sells the product
     *
     * @var mixed
     */
    public mixed $vendor;

    /**
     * Product
     *
     * @var
     */
    public $product;

    /**
     * Complete instance of a purchase
     *
     *
     */
    public Purchase $purchase;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        $this->buyer = $purchase->buyer;
        $this->vendor = $purchase->vendor;
        $this->product = $purchase->offer->product;
        $this->purchase = $purchase;
    }

}
