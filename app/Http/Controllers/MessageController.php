<?php

namespace App\Http\Controllers;

use App\Exceptions\RequestException;
use App\Http\Requests\Profile\DecryptMessagesRequest;
use App\Http\Requests\Profile\NewConversationRequest;
use App\Http\Requests\Profile\NewMessageRequest;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Extends profile controller because needs all middleware
 *
 * Class MessageController
 * @package App\Http\Controllers
 */

class MessageController extends ProfileController
{
    /**
     * MessageController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // Must be logged in
        $this -> middleware('auth');
    }

    /**
     * Returns the view with the all conversations and view of the one conversation if it is set
     *
     * @param Conversation|null $conversation
     * @param Request $request
     * @return Factory|View
     * @throws AuthorizationException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function messages(Request $request,Conversation $conversation = null): Factory|View
    {
        if (!is_null($conversation)) {
            // only people in chat can view conversation
            $this->authorize('view', $conversation);

            // Mark messages as read
            $conversation->markMessagesAsRead();
        }
        $other_party_from_url = $request->input('otherParty');

        $other_party_from_session = session()->get('new_conversation_other_party');
        if (!$other_party_from_session){
            $new_conversation_other_party = $other_party_from_url;
        } else {
            session()->forget('new_conversation_other_party');
            $new_conversation_other_party = $other_party_from_session;
        }

        return view('profile.messages', [
            'new_conversation_other_party' => $new_conversation_other_party,
            'conversation' => $conversation,
            'usersConversations' => auth() -> user() -> conversations() -> orderByDesc('updated_at') -> take(10) -> get(), // list of users' conversations
            'conversationMessages' => $conversation?->messages()->orderByDesc('created_at')
                ->paginate(config('marketplace.products_per_page')), // messages of the conversation
        ]);

    }

    /**
     *  List of all paginated Conversations
     *
     *  @return view
     */
    public function listConversations(): View
    {
        return view('profile.conversations', [
            'usersConversations' => auth() -> user() -> conversations() -> orderByDesc('updated_at') -> paginate(config('marketplace.products_per_page')),
        ]);
    }

    /**
     * Find old conversation or make new and redirect the page
     *
     * @param NewConversationRequest $request
     * @return RedirectResponse
     */
    public function startConversation(NewConversationRequest $request): RedirectResponse
    {
        $otherUser = User::where('username', $request -> username) -> first();

        $newOldConversation = Conversation::findWithUsersOrCreate(auth() -> user(), $otherUser);


        // Redirect to a new message via GET request
        return redirect() -> route('profile.messages.send.message', [
            'conversation' => $newOldConversation,
            'message' => $request -> message
        ]) ;
    }

    /**
     * Request for the new message, POST
     * Response is redirect back
     *
     * @param NewMessageRequest $request
     * @param Conversation $conversation
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function newMessage(NewMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        try{
            $this -> authorize('update', $conversation);
            $conversation -> updateTime(); // update time of the conversation
            $request -> persist($conversation); // Persist the request
            session() -> flash('success', 'New message has been posted');
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        // Redirect to conversation
        return redirect() -> route('profile.messages', $conversation);
    }

    /**
     * Shows a page that requests password to decrypt rsa key
     */
    public function decryptKeyShow(Request $request): View
    {

        return view('profile.messagekey');
    }

    /**
     * Shows a page that requests password to decrypt rsa key
     * @throws \Throwable
     */
    public function decryptKeyPost(DecryptMessagesRequest $request): RedirectResponse
    {
        try{
            $request->persist();
        } catch(RequestException $e){
            $e -> flashError();
            return redirect()->back();
        }
        return redirect()->route('profile.messages');

    }
}
