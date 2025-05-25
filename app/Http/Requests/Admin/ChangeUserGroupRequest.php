<?php

namespace App\Http\Requests\Admin;

use App\Events\Admin\UserGroupChanged;
use App\Exceptions\RequestException;
use App\Marketplace\Payment\FinalizeEarlyPayment;
use App\Models\Admin;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $permissions
 * @property mixed $administrator
 * @property mixed $vendor
 * @property mixed $canUseFe
 */
class ChangeUserGroupRequest extends FormRequest
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
            'permissions' => 'array|nullable'
        ];
    }

    /**
     * @throws RequestException
     * @throws \Throwable
     */
    public function persist(User $user): void
    {
        $permissions = $this -> permissions;
        if(!is_array($permissions)){
            $permissions = []; // empty array
        }

        $user -> setPermissions($permissions);
        $this->updateAdministrator($user);
        $this->updateVendor($user);
        if ($user->vendor != null){
            $this->updateFinalizeEarly($user);
        }



        session()->flash('success', 'Successfully updated ' . $user->username . '\'s user groups and permissions');
    }

    /**
     *  If an administrator flag is present and user is not administrator, make him one
     *  If an administrator flag is not present, and user is administrator remove him admin
     *
     */
    public function updateAdministrator(User $user): void
    {

        // User is not admin, should change
        if ($this->administrator == 'adminChecked' && !$user->isAdmin()) {
            $nowTime = Carbon::now();
            Admin::insert([
                'id' => $user->id,
                'created_at' => $nowTime,
                'updated_at' => $nowTime
            ]);
            event(new UserGroupChanged($user, 'administrator', true, auth()->user()));
        }

        if ($this->administrator !== 'adminChecked' && $user->isAdmin()){
            $admin = Admin::query()->find($user->id);
            if ($admin !== null) {
                $admin->delete();
                event(new UserGroupChanged($user, 'administrator', false, auth()->user()));
            }
        }
    }

    /**
     *  If a vendor flag is present and user is not vendor, make him one
     *  If a vendor flag is not present, and the user is vendor remove his vendor access
     *
     */
    public function updateVendor(User $user): void
    {
        // User is not admin, should change
        if ($this->vendor == 'vendorChecked' && !$user->isVendor()) {
            Vendor::insert([
                'id' => $user -> id,
                'vendor_level' => 0,
                'about' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            event(new UserGroupChanged($user, 'vendor', true, auth()->user()));
        }

        if ($this->vendor !== 'vendorChecked' && $user->isVendor()){
            $vendor = Vendor::query()->find($user->id);
            if ($vendor !== null) {
                $vendor->delete();
                event(new UserGroupChanged($user, 'vendor', false, auth()->user()));
            }
        }
    }

    public function updateFinalizeEarly(User $user): void
    {
        if (!FinalizeEarlyPayment::isEnabled())
            return;
        if($this->canUseFe == 'feChecked' && $user->isVendor() && !$user->vendor->canUseFe()){
            $user->vendor->can_use_fe = 1;
            $user->vendor->save();
        } else {
            $user->vendor->can_use_fe = 0;
            $user->vendor->save();
        }


    }
}
