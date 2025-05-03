<?php

namespace App\Http\Requests\Profile;

use App\Exceptions\RequestException;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

/**
 * @property mixed $old_password
 * @property mixed $new_password
 */
class ChangePasswordRequest extends FormRequest
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
            'old_password' => 'required|string',
            'new_password' => 'required|string|confirmed|min:8',
        ];
    }

    /**
     * @throws EnvironmentIsBrokenException
     * @throws WrongKeyOrModifiedCiphertextException
     * @throws RequestException
     */
    public function persist(): void
    {
        $user = auth()->user();
        if (Hash::check($this->old_password,$user->password))
        {
            //change a user's password
            $user->password = Hash::make($this->new_password);

            //re-encrypt a user's private key with a new password
            $decryptedPrivateKey = Crypto::decryptWithPassword($user->msg_private_key, $this->old_password);
            $user->msg_private_key = Crypto::encryptWithPassword($decryptedPrivateKey, $this->new_password);

            // save changes
            $user -> save();


            session() -> flash('success', 'You have successfully changed your password!');
        }
        else
        {
            throw new RequestException("Old password is incorrect");
        }
    }
}
