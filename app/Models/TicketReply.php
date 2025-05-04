<?php

namespace App\Models;

use App\Events\Support\NewTicketReply;
use App\Exceptions\RequestException;
use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $text
 * @property mixed $ticket_id
 * @property int|mixed $user_id
 * @property mixed $ticket
 * @property mixed $created_at
 */
class TicketReply extends Model
{
    use Uuids;

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * Persisting new Ticket Reply in ticket
     *
     * @param Ticket $ticket
     * @param $message
     * @throws \Throwable
     */
    public static function postReply(Ticket $ticket, $message): void
    {
        if(!auth() -> check())
            throw new RequestException("There is no logged user!");

        throw_if($ticket -> solved, new RequestException("Ticket is solved, you can't post any messages!"));

        // if logged user is not the one who posted it
        if(auth() -> user() -> id != $ticket -> user -> id){
            // mark the title as answered
            $ticket -> answered = true;
            $ticket -> save();
        }

        $newReply = new TicketReply;
        $newReply -> text = $message;
        $newReply -> ticket_id = $ticket -> id;
        $newReply -> user_id = auth() -> user() -> id;

        $newReply -> save();
        if ($newReply->ticket->user->id !== $newReply->user_id){
            event(new NewTicketReply($newReply));
        }

    }

    /**
     * Relationship with the User who posted the reply
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this -> belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Ticket that owns this reply
     *
     * @return BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this -> belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Time passed
     *
     * @return string
     */
    public function getTimePassedAttribute(): string
    {
        return Carbon::parse($this -> created_at) -> diffForHumans();
    }
}
