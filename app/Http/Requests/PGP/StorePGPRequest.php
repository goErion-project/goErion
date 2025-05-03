<?php

namespace App\Http\Requests\PGP;

use App\Exceptions\RequestException;
use App\Marketplace\PGP;
use App\Models\PGPKey;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $validation_number
 */
class StorePGPRequest extends FormRequest
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
            'validation_number' => 'required|numeric'
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function persist(): void
    {
        //validation number is ok
        $correctValidationNumber = session()->get(PGP::NEW_PGP_VALIDATION_NUMBER_KEY);
        if ($correctValidationNumber == $this->validation_number){
            try {
                //start transaction
                DB::beginTransaction();
                if (auth()->user()->hasPGP())
                {
                    //save an old pgp key
                    $savingOldKey = new PGPKey();
                    $savingOldKey->key = auth()->user()->pgp_key;
                    $savingOldKey->user_id = auth()->user()->id;
                    $savingOldKey->save();
                }
                //change users' key
                auth()->user()->pgp_key = session()->get(PGP::NEW_PGP_SESSION_KEY);
                auth()->user()->save();

                //commit changes
                DB::commit();
            }
            catch (\Exception $e)
            {
                DB::rollBack();
                throw new RequestException('Something went wrong, please try again later!.');
            }
            //forget session data
            session()->forget(PGP::NEW_PGP_ENCRYPTED_MESSAGE);
            session()->forget(PGP::NEW_PGP_SESSION_KEY);
            session()->forget(PGP::NEW_PGP_VALIDATION_NUMBER_KEY);
        }
        else{
            throw new RequestException('Invalid validation number');
        }
    }
}
