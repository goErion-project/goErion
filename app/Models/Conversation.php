<?php

namespace App\Models;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int|mixed $receiver_id
 * @property int|mixed $sender_id
 * @property mixed $receiver
 * @property mixed $sender
 * @property Carbon $updated_at
 * @property mixed $id
 */
class Conversation extends Model
{
    use Uuids;
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * Create conversation for a mass message, Sender is null
     *
     * @param User $receiver
     * @return Conversation
     */
    public static function findOrCreateMassMessageConversation(User $receiver): Conversation
    {
        /**
         * Find conversation with this receiver
         */
        $oldConversation = Conversation::query()->whereNull('sender_id')
            -> where('receiver_id', $receiver -> id)
            -> first();
        // update 'updated_at' timestamp for ordering mass message conversations
        if ($oldConversation != null){
            $oldConversation->updated_at = Carbon::now();
            $oldConversation->save();
        }

        if($oldConversation == null){
            $oldConversation = new Conversation;
            $oldConversation -> receiver_id = $receiver -> id;
            $oldConversation -> save();
        }

        return $oldConversation;
    }

    /**
     * Find the conversation between these two users or create one
     *
     * @param User $sender
     * @param User $receiver
     * @return Conversation
     */
    static public function findWithUsersOrCreate(User $sender, User $receiver) : Conversation
    {
        /**
         * Find conversation with any combinations of sender and receiver users
         */
        $oldConversation = Conversation::query()->where(function($q) use ($sender, $receiver){
            $q -> where('sender_id', $sender -> id);
            $q -> where('receiver_id', $receiver -> id);
        })
            -> orWhere(function ($q) use ($sender, $receiver) {
                $q -> where('sender_id', $receiver -> id);
                $q -> where('receiver_id', $sender -> id);
            }) -> first();

        /**
         * If it is not found, make new conversation
         */
        if($oldConversation == null)
        {
            $oldConversation = new Conversation;
            $oldConversation -> sender_id = $sender -> id;
            $oldConversation -> receiver_id = $receiver -> id;
            $oldConversation -> save();
        }

        return $oldConversation;
    }

    /**
     * Relationship with the messages
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this -> hasMany(Message::class, 'conversation_id', 'id');
    }


    /**
     * Relationship with the User, as a sender
     *
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this -> belongsTo(User::class, 'sender_id', 'id');
    }

    /**
     * Relationship with User as a Receiver
     *
     * @return BelongsTo
     */
    public function receiver(): BelongsTo
    {
        return $this -> belongsTo(User::class,'receiver_id', 'id');
    }

    /**
     * Returns if market and starts the conversation
     *
     * @return bool
     */
    public function isMassConversation() : bool
    {
        return $this -> sender_id == null;
    }

    /**
     * Returns the not logged user in the conversation
     * if the message from the market returns the stub user with a fake username
     *
     * @return User
     */
    public function otherUser() : User
    {
        // if the logged user is a receiver return sender
        if(auth() -> check() && auth() -> user()  == $this -> receiver)
            if($this -> sender)
                return $this -> sender;
            // if the sender is null, then return stub user
            else
                return User::stub(); // non-persisted user

        // another user is a receiver
        return $this -> receiver;


    }

    /**
     * Count all messages in the conversation that are not user's and not read
     *
     * @return int
     */
    public function unreadMessages() : int
    {
        return $this -> messages() -> where('sender_id', '!=', auth() -> user() -> id) -> where('read', false) -> count();
    }

    /**
     * Mark unread messages as read in this conversation
     */
    public function markMessagesAsRead(): void
    {
        $this -> messages() -> where('receiver_id', auth() -> user() -> id)
            -> where('read', false) -> update(['read' => true]);
    }

    /**
     * Update the time of the conversation
     */
    public function updateTime(): void
    {
        $this -> updated_at = Carbon::now();
        $this -> save();
    }

    /**
     * Return dated string for an interval of the last message
     *
     * @return string
     */
    public function getUpdatedAgoAttribute(): string
    {
        // if there are no messages, return the time of creation
        if($this->messages()->get()->isEmpty())
            return Carbon::parse($this->updated_at) -> diffForHumans();
        return Carbon::parse($this -> messages() -> orderByDesc('created_at') -> first() -> created_at) -> diffForHumans();

    }
}
