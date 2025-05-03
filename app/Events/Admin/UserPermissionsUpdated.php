<?php

namespace App\Events\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserPermissionsUpdated
{
    use Dispatchable;

    /**
     * Admin performing request
     *
     * @var User
     */
    public Admin|User $admin;

    /**
     * User request is being performed on
     *
     * @var User
     */
    public User $user;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Admin $admin)
    {
        $this->user = $user;
        $this->admin = $admin;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|PrivateChannel|array
     */
    public function broadcastOn(): Channel|PrivateChannel|array
    {
        return new PrivateChannel('channel-name');
    }
}
