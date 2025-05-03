<?php

namespace App\Listeners;

use App\Events\Message\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMessageNotifications
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
     * @param  MessageSent  $event
     * @return void
     */
    public function handle(MessageSent $event): void
    {
        $content = 'You have received new message from ['.$event->message->getSender()->username.']';
        $routeName = 'profile.messages';
        $routeParams = serialize(['conversation'=>$event->message->conversation()->first()->id]);
        $event->message->getReceiver()->notify($content,$routeName,$routeParams);
    }
}
