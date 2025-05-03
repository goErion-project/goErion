<?php

namespace App\Http\Requests\Admin;

use App\Events\Message\MessageSent;
use App\Exceptions\RequestException;
use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $groups
 * @property mixed $message
 */
class SendMessagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|string',
            'groups' => 'required|array|min:1',
            'encrypted' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'message.min' => 'At least one group must be selected!'
        ];
    }

    /**
     * @throws RequestException
     */
    public function persist(): int
    {

        // make collections of receivers
        $receivers = collect();

        // add admins id
        if(in_array('admins', $this -> groups)){
            $receivers = $receivers -> merge(Admin::allUsers());
        }

        // add vendors
        if(in_array('vendors', $this -> groups)){
            $receivers = $receivers -> merge(Vendor::allUsers());
        }

        // buyers
        if(in_array('buyers', $this -> groups)){
            $receivers = $receivers -> merge(User::buyers());
        }

        if($receivers -> isEmpty())
            throw new RequestException('There are no users in selected group/s.');


        $receivers = $receivers -> unique(function($receiver){
            return $receiver -> id;
        });

        // Create conversations
        foreach ($receivers as $receiver) {

            $newConversation = Conversation::findOrCreateMassMessageConversation($receiver);

            $newMessage = new Message;
            $newMessage -> setConversation($newConversation);
            $newMessage -> setReceiver($receiver);
            $newMessage -> setMassMessageContent($this -> message, $receiver);
            $newMessage -> save();

            event(new MessageSent($newMessage));

        }

        return $receivers -> count();

    }
}
