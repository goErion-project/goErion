<?php

namespace App\Http\Requests\Profile;

use App\Events\Message\MessageSent;
use App\Exceptions\RequestException;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $message
 */
class NewMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|string'
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws RequestException
     * @throws NotFoundExceptionInterface
     */
    public function persist(Conversation $conversation): void
    {
        $sender = auth() -> user();
        $receiver = $conversation -> otherUser();
        $newMessage = new Message;
        $newMessage -> setConversation($conversation);
        $newMessage -> setSender($sender);
        $newMessage -> setReceiver($receiver);
        $newMessage -> setContent($this -> message,$sender,$receiver);
        $newMessage -> save();
        event(new MessageSent($newMessage));
    }
}
