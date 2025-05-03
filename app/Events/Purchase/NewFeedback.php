<?php

namespace App\Events\Purchase;

use App\Models\Feedback;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewFeedback
{
    use Dispatchable;

    /**
     * Feedback that triggered the event
     *
     * @var Feedback
     */
    public Feedback $feedback;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }
}
