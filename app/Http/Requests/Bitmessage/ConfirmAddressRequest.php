<?php

namespace App\Http\Requests\Bitmessage;

use App\Exceptions\RequestException;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $code
 */
class ConfirmAddressRequest extends FormRequest
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
            'code' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Confirmation code is required'
        ];
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws RequestException
     * @throws ContainerExceptionInterface
     */
    public function persist(): void
    {
        if (!session()->has('bitmessage_confirmation')){
            throw new RequestException('Something went wrong, try again later');
        }
        $data = session()->get('bitmessage_confirmation');
        $time = Carbon::parse($data['time']);
        $validTime = config('bitmessage.confirmation_valid_time');
        if ($time->diffInMinutes(Carbon::now()) > $validTime){
            session()->forget('bitmessage_confirmation');
            throw new RequestException('Code we sent is no longer valid, send new confirmation code');
        }
        if ($this->code !== $data['code']){
            throw new RequestException('Code is not valid');
        }
        $user = auth()->user();
        $user->bitmessage_address = $data['address'];
        $user->save();
        session()->flash('success','Address confirmed successfully');
        session()->forget('bitmessage_confirmation');
    }
}
