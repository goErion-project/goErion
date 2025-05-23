<?php

namespace App\Events\Admin;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserGroupChanged
{
    use Dispatchable;

    /**
     * Admin performing request
     *
     * @var User
     */
    public User $admin;

    /**
     * User request is being performed on
     *
     * @var User
     */
    public User $user;

    /**
     * User group name
     *
     * @var string
     */
    public string $userGroup;

    /**
     * Status
     * true = given
     * false = taken
     *
     * @var bool
     */
    public bool $status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user,string $userGroup,bool $status,User $admin)
    {
        $this->admin = $admin;
        $this->user = $user;
        $this->userGroup = $userGroup;
        $this->status = $status;

    }

}
