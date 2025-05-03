<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\RequestException;
use App\Marketplace\PGP;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;

/**
 * @property mixed $username
 */
class RecoverPasswordPgpRequest extends FormRequest
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
            'username' => 'required|exists:users,username'
        ];
    }

    public function messages(): array
    {
        return[
            'username.required' => 'Username is required',
            'username.exists' => 'User with that username does not exist',
        ];
    }

    /**
     * @throws RequestException
     */
    public function persist(): RedirectResponse
    {
        $user = User::where('username', $this->username)->first();
        //check if a user exists
        if ($user == null) {
            throw new RequestException('Could not find user with that username');
        }

        if($user->pgp_key==null){
            throw new RequestException('This user does not have pgp key');
        }

        $validationNumber = rand(100000000000, 999999999999); // Random number to confirm
        $decryptedMessage = "You have successfully decrypted this message.\nTo validate this key please copy validation number to the field on the site\nValidation number:". $validationNumber;
        // Encrypt throws \Exception
        try{
            $encryptedMessage = PGP::EncryptMessage($decryptedMessage, $user->pgp_key);
        }
        catch (\Exception $e){
            throw new RequestException($e -> getMessage());
        }

        // store data to sessions

        session() -> put(PGP::NEW_PGP_VALIDATION_NUMBER_KEY, $validationNumber );
        session() -> put(PGP::NEW_PGP_ENCRYPTED_MESSAGE, $encryptedMessage);
        session(
            [
                'username'=>$user->username
            ]
        );

        return redirect() -> route('auth.pgprecover');
    }
}
