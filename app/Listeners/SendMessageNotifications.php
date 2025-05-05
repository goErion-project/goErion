<?php

namespace App\Listeners;

use App\Events\Message\MessageSent;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendMessageNotifications
{
    /**
     * Handle the event when a message is sent.
     *
     * @param  MessageSent  $event
     * @return void
     */
    public function handle(MessageSent $event): void
    {
        $message = $event->message;

        $conversation = $message->conversation;
        if (!$conversation) {
            return;
        }

        $senderId = $message->sender_id;
        $receiverId = $message->receiver_id;

        if (!$senderId || !$receiverId) {
            return;
        }

        try {
            $sender = $message->getSender() ?? User::stub();
            $receiver = $message->getReceiver();

            if (!is_object($receiver)) {
                Log::warning("Receiver is not a valid user object for message ID {$message->id}");
                return;
            }

            $content = 'You have received a new message from [' . $sender->username . ']';
            $routeName = 'profile.messages';
            $routeParams = serialize(['conversation' => $conversation->id]);

            $receiver->notify($content, $routeName, $routeParams);
        } catch (\Throwable $e) {
            Log::error("Failed to send notification for message ID {$message->id}: " . $e->getMessage());
        }
    }
}
