<?php

namespace App\Models;

use App\Exceptions\RequestException;
use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed|string $title
 * @property false|mixed $answered
 * @property int|mixed $user_id
 * @property mixed $created_at
 * @property mixed $solved
 * @property mixed $user
 * @property mixed $id
 */
class Ticket extends Model
{
    use Uuids;

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    /**
     * Opens a new ticket with the title of logged user
     *
     * @param string $title
     * @return Ticket
     * @throws RequestException
     */
    public static function openTicket(string $title) : Ticket
    {
        if(!auth() -> check())
            throw new RequestException("There is no logged user!");

        $newTicket = new Ticket;
        $newTicket -> title = $title;
        $newTicket -> answered = false;
        $newTicket -> user_id = auth() -> user() -> id;
        $newTicket -> save();

        return $newTicket;
    }


    /**
     * Relationship of the user who made a Ticket
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this -> belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with the Ticket replies
     *
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this -> hasMany(TicketReply::class, 'ticket_id', 'id');
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
