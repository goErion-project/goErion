<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConversationPolicy
{
    use HandlesAuthorization;

    /**
     * Determines if the user can do all actions, admin can do all actions
     *
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability): bool
    {
        if($user -> isAdmin()) return true;
    }

    /**
     * Determine whether the user can view the conversation.
     *
     * @param  User  $user
     * @param  Conversation  $conversation
     * @return bool
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation -> sender_id == $user -> id || $conversation -> receiver_id == $user -> id;
    }

    /**
     * Determine whether the user can create conversations.
     *
     * @param  User  $user
     * @return void
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the conversation.
     *
     * @param  User  $user
     * @param  Conversation  $conversation
     * @return bool
     */
    public function update(User $user, Conversation $conversation): bool
    {
        return $conversation -> sender_id == $user -> id || $conversation -> receiver_id == $user -> id;
    }

    /**
     * Determine whether the user can delete the conversation.
     *
     * @param  User  $user
     * @param  Conversation  $conversation
     * @return void
     */
    public function delete(User $user, Conversation $conversation)
    {
        //
    }

    /**
     * Determine whether the user can restore the conversation.
     *
     * @param  User  $user
     * @param  Conversation  $conversation
     * @return void
     */
    public function restore(User $user, Conversation $conversation)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the conversation.
     *
     * @param  User  $user
     * @param  Conversation  $conversation
     * @return void
     */
    public function forceDelete(User $user, Conversation $conversation)
    {
        //
    }
}
