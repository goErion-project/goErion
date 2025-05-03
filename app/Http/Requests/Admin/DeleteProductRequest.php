<?php

namespace App\Http\Requests\Admin;

use App\Events\Admin\ProductDeleted;
use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $product_id
 */
class DeleteProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id'
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required',
            'product_id.exists' => 'Product with that ID does not exist'
        ];
    }
    public function persist(): void
    {
        $product = Product::query()->where('id',$this->product_id)->with('user')->first();
        event(new ProductDeleted($product,$product->user,auth()->user()));
        $product->delete();
        session() -> flash('success', 'You have successfully deleted product!');
    }
}
