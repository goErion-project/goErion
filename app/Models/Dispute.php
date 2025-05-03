<?php

namespace App\Models;

use App\Exceptions\RequestException;
use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property mixed $purchase
 * @property mixed $winner_id
 * @property mixed $winner
 * @property mixed $created_at
 * @property mixed $id
 */
class Dispute extends Model
{
    use Uuids;
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * Messages of the dispute
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this -> hasMany(DisputeMessage::class, 'dispute_id');
    }


    /**
     * Purchase
     *
     * @return HasOne
     */
    public function purchase(): HasOne
    {
        return $this -> hasOne(Purchase::class, 'dispute_id', 'id');
    }

    /**
     * Returns if the user can post a message on the dispute
     *
     * @return bool
     */
    public function canPostMessage() : bool
    {
        // if it is not logged in
        if(!auth()->check()) return false;

        // Define when the user can post
        if($this -> purchase -> isVendor()) return true;
        if($this -> purchase -> isBuyer()) return true;
        if(auth() -> user() -> isAdmin()) return true;

        return false;

    }

    /**
     * Posts new message to this dispute
     *
     * @param string $message
     * @throws RequestException
     */
    public function newMessage(string $message): void
    {
        if(!auth() -> check())
            throw new RequestException('You must be logged in to send new message!');

        if(!$this -> canPostMessage())
            throw new RequestException('You can\'t post messages to this dispute');

        if($this -> isResolved())
            throw new RequestException('Can\'t post new messages when it is resolved');

        $newMessage = new DisputeMessage;
        $newMessage -> message = $message;
        $newMessage -> setDispute($this);
        $newMessage -> setAuthor(auth() -> user());
        $newMessage -> save();

    }

    /**
     * Returns if the dispute is resolved
     *
     * @return bool
     */
    public function isResolved(): bool
    {
        return $this -> winner_id != null;
    }

    /**
     * Returns the winner of the dispute, can be null
     *
     * @return HasOne
     */
    public function winner(): HasOne
    {
        return $this -> hasOne(User::class, 'id', 'winner_id');
    }

    /**
     * Returns true if the logged user is the winner of this purchase
     *
     * @return bool
     */
    public function isWinner() : bool
    {
        // if a user is logged in
        if(auth()->check())
            return auth()->user()->id == $this->winner->id;
        return false;
    }


    /**
     * Time difference since the opened dispute
     *
     * @return string
     */
    public function timeDiff(): string
    {
        return Carbon::parse($this -> created_at) -> diffForHumans();
    }

}
