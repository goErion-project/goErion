<?php

namespace App\Http\Requests\Product;

use App\Marketplace\Utility\CurrencyConverter;
use App\Models\Product;
use App\Models\Shipping;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $name
 * @property mixed $duration
 * @property mixed $from_quantity
 * @property mixed $to_quantity
 * @property mixed $price
 */
class NewShippingRequest extends FormRequest
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
            'name' => 'required|string',
            'duration' => 'required|string',
            'price' => 'required|numeric',
            'from_quantity' => 'required|numeric|min:1',
            'to_quantity' => 'required|numeric|min:1'
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function persist(Product $product = null): void
    {
        // get product from session
        $productsShippings = session('product_shippings') ?? collect();

        $newShipping = new Shipping;
        $newShipping -> name = $this -> name;
        $newShipping -> duration = $this -> duration;
        $newShipping -> from_quantity = $this -> from_quantity;
        $newShipping -> to_quantity = $this -> to_quantity;
        $newShipping -> price = CurrencyConverter::convertToUsd($this -> price);

        // shippings on existing product
        if($product && $product -> exists()){
            $newShipping -> setProduct($product);
            $newShipping -> save();
        }
        // shippings on new product
        else{
            $productsShippings -> push($newShipping); // put new offer
            $productsShippings = $productsShippings -> sortBy(function($shipment){ return $shipment -> price; });

            session() -> put('product_shippings', $productsShippings);
        }


    }
}
