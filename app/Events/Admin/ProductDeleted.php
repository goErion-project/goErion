<?php

namespace App\Events\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductDeleted
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    /**
     * Admin performing the request
     *
     * @var User
     */
    public User $admin;

    /**
     * Product being deleted
     *
     * @var Product
     */
    public Product $product;

    /**
     * Vendor owning the product
     *
     * @var User
     */
    public User $vendor;

    public function __construct(Product $product,User $vendor,User $admin)
    {
        $this->product = $product;
        $this->admin = $admin;
        $this->vendor = $vendor;
    }
}
