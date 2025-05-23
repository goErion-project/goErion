<?php

namespace App\Http\Requests\Auth;

use App\Marketplace\Encryption\Keypair;
use App\Marketplace\PGP;
use App\Models\User;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $validation_string
 * @property mixed $password
 */
class ResetPasswordPgpRequest extends FormRequest
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
            'validation_string' => 'required|numeric',
            'password' => 'required|string|confirmed|min:8'
        ];
    }

    public function messages(): array
    {
        return[
            'validation_string.required'=> 'Validation number is required',
            'password.required'=>'Password is required',
            'password.confirmed' => 'You didn\'t confirm password correctly!',
            'password.min' => 'Password must have at least 8 characters',
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|\SodiumException
     * @throws EnvironmentIsBrokenException
     */
    public function persist(){
        $correctValidationNumber = session() -> get(PGP::NEW_PGP_VALIDATION_NUMBER_KEY);
        if($correctValidationNumber == $this -> validation_string){
            $user=User::where('username', session()->get('username'))->first();
            $user->password = bcrypt($this -> password);

            // generate a new key pair
            $keyPair = new Keypair();
            $privateKey = $keyPair->getPrivateKey();
            $publicKey =   $keyPair->getPublicKey();
            // encrypt private key with user's password
            $encryptedPrivateKey = Crypto::encryptWithPassword($privateKey, $this->password);
            $user->msg_public_key = encrypt($publicKey);
            $user->msg_private_key = $encryptedPrivateKey;

            $user->save();
            session() -> flash('success', 'You have successfully changed your password!');
            return redirect() -> route('auth.signin');
        }
    }
}
