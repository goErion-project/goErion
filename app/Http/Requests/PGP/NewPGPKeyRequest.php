<?php

namespace App\Http\Requests\PGP;

use App\Exceptions\RequestException;
use App\Marketplace\PGP;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $newpgp
 */
class NewPGPKeyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'newpgp.string'=>'You must enter your PGP key!'
        ];
    }

    /**
     * @throws \Exception
     */
    public function persist(): void
    {
        $newUsersPGP = $this->newpgp;
        $validationNumber = rand(100000000000, 999999999999);//random number to confirm
        $decryptedMessage = "You have successfully decrypted your message.\nTo validate this message, please enter the following number: $validationNumber";
        //Encrypt throws \Exception
        try {
            $encryptedMessage = PGP::EncryptMessage($decryptedMessage,$newUsersPGP);
        }
        catch (\Exception $e) {
            throw new RequestException($e->getMessage());
        }

        //store data to sessions
        session()->put(PGP::NEW_PGP_VALIDATION_NUMBER_KEY, $validationNumber);
        session()->put(PGP::NEW_PGP_SESSION_KEY, $newUsersPGP);
        session()->put(PGP::NEW_PGP_ENCRYPTED_MESSAGE, $encryptedMessage);
    }
}
