<?php

namespace App\Http\Requests\Product;

use App\Models\PhysicalProduct;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

/**
 * @property mixed $country_from
 * @property mixed $countries_option
 * @property mixed $countries
 */
class NewShippingOptionsRequest extends FormRequest
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
            'country_from' => ['required', Rule::in(array_keys(config('countries')))],
            'countries_option' => ['required', Rule::in(array_keys(PhysicalProduct::$countriesOptions))],
            'countries' => 'array|nullable'
        ];
    }

    public function persist(PhysicalProduct $product = null): RedirectResponse
    {
        // product is not set = new product from a session
        if(!$product || !$product -> exists()) {
            // get product from session
            $product = session('product_details') ?? new PhysicalProduct();
            if (!($product instanceof PhysicalProduct)) {
                $product = new PhysicalProduct;
            }
        }
        // set parameters
        $product -> country_from = $this -> country_from;
        $product -> countries_option = $this -> countries_option;
        $product -> setCountries($this -> countries); // empty string if it is array empty


        // new product
        if(!$product ->exists){
            session() -> put('product_details', $product);
            return redirect() -> route('profile.vendor.product.images');
        }

        $product -> save();
        session() -> flash('success', 'You have successfully changed shipping options!');
        return redirect() -> back();
    }
}
