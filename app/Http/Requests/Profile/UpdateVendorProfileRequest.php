<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

/**
 * @property mixed $profilebg
 * @property mixed $description
 */
class UpdateVendorProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isVendor();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'max:120',
        ];
    }

    public function persist(): void
    {

        $pofile_bgs = config('vendor.profile_bgs');
        $bg =$this->profilebg;
        if ($bg == null){
            $bg = Arr::random($pofile_bgs);
        } else {
            $bg = $pofile_bgs[$bg];
        }
        $vendor =  $this->user()->vendor;
        $vendor->about = $this->description;
        $vendor->profilebg = $bg;
        $vendor->save();

        session()->flash('success','Vendor profile updated successfully');

    }
}
