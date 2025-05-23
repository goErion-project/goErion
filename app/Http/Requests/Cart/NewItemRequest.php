<?php

namespace App\Http\Requests\Cart;

use App\Exceptions\RequestException;
use App\Marketplace\Cart;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property mixed $amount
 * @property mixed $delivery
 * @property mixed $coin
 * @property mixed $message
 * @property mixed $type
 */
class NewItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'delivery' => 'nullable|exists:shippings,id',
            'amount' => 'numeric|required',
            'message' => 'nullable|string',
            'coin' => ['required' , Rule::in(array_keys(config('coins.coin_list')))],
            'type' => ['required', Rule::in(array_keys(Purchase::$types))],
        ];
    }

    /**
     * @throws \Throwable
     */
    public function persist(Product $product): void
    {
        $shipping = null;
        throw_if($product->user->id == auth()->user()->id, new RequestException('You can\'t put your products in cart!'));
        // select shipping
        if($product -> isPhysical())
            $shipping = $product -> specificProduct() -> shippings()
                -> where('id', $this -> delivery)
                -> where('deleted', '=', 0) // is not deleted
                -> first();
        Cart::getCart() -> addToCart($product, $this -> amount, $this -> coin, $shipping, $this -> message, $this -> type);
    }
}
