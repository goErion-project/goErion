<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\RequestException;
use Hash;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $validation_string
 */
class VerifySinginRequest extends FormRequest
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
            'validation_string' => 'required|string|max:10'
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RequestException
     */
    public function persist(): RedirectResponse
    {
        if (Hash::check($this->validation_string, session()->get('login_validation_string')))
        {
            session()->forget('login_validation_string');
            session()->forget('login_encrypted_message');
            return redirect()->route('profile.index');
        }
        else
            throw new RequestException("Invalid validation string");
        return redirect()->back();
    }
}
