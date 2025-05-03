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

class CanceledPurchase
{
    use Dispatchable;

    public Purchase $purchase;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }
}
