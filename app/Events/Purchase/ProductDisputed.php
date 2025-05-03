<?php

namespace App\Events\Purchase;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductDisputed
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
     * User that initiated dispute
     *
     * @var User
     */
    public User $initiator;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase,User $user) {

        $this->buyer = $purchase->buyer;
        $this->vendor = $purchase->vendor;
        $this->product = $purchase->offer->product;
        $this->purchase = $purchase;

        $this->initiator = $user;
    }

}
