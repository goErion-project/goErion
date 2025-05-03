<?php

namespace App\Http\Requests\Admin;

use App\Events\Admin\UserEdited;
use App\Exceptions\RequestException;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $username
 * @property mixed $referral_code
 */
class ChangeBasicInfoRequest extends FormRequest
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
            //
        ];
    }

    /**
     * @throws RequestException
     */
    public function persist(User $user): void
    {
        $this->updateUsername($user);
        $this->updateReferralCode($user);
        session()->flash('success', 'Successfully updated ' . $user->username . '\'s basic info');
    }

    /**
     * @throws RequestException
     */
    public function updateUsername(User $user): void
    {

        if ($this->username !== null && $this->username !== $user->username){
            // check if user with username already exists
            $userCheck = User::where('username',$this->username)->first();
            if ($userCheck !== null)
                throw new RequestException('User with that username already exists');
            $oldUser = User::query()->find($user->id);
            $user->username = $this->username;
            $user->save();
            event(new UserEdited($oldUser,$user,auth()->user()));

        }
    }

    /**
     * @throws RequestException
     */
    public function updateReferralCode(User $user): void
    {
        if ($this->referral_code !== null && $this->referral_code !== $user->referral_code){
            // check if a user with that code already exists
            $userCheck = User::where('referral_code',$this->referral_code)->first();
            if ($userCheck !== null)
                throw new RequestException('User with that referral code already exists');
            $oldUser = User::query()->find($user->id);
            $user->referral_code = $this->referral_code;
            $user->save();
            event(new UserEdited($oldUser,$user,auth()->user()));
        }
    }
}
