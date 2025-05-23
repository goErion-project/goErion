<?php

namespace App\Listeners;

use App\Events\Support\NewTicketReply;
use App\Events\Support\TicketClosed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SupportEventSubscriber
{
    public function onNewTicketMessage(NewTicketReply $event): void
    {
        $content = 'There is a new reply on your support ticket by ['.$event->ticketReply->user->username.']';
        $routeName = 'profile.tickets';
        $routeParams = serialize(['ticket'=>$event->ticketReply->ticket->id]);
        $event->ticketReply->ticket->user->notify($content,$routeName,$routeParams);
    }


    public function onTicketClosed(TicketClosed $event): void
    {
        $content = 'Your support ticket has been closed by administrator';
        $routeName = 'profile.tickets';
        $routeParams = serialize(['ticket'=>$event->ticket->id]);
        $event->ticket->user->notify($content,$routeName,$routeParams);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  $events
     * @return void
     */
    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Support\NewTicketReply',
            'App\Listeners\SupportEventSubscriber@onNewTicketMEssage'
        );

        $events->listen(
            'App\Events\Support\TicketClosed',
            'App\Listeners\SupportEventSubscriber@onTicketClosed'
        );

    }

}
