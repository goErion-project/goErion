<?php

namespace App\Http\Requests\Product;

use App\Models\DigitalProduct;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $product_content
 */
class NewDigitalRequest extends FormRequest
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
            'product_content' => 'string|nullable',
            'autodelivery' => 'boolean|nullable',
            'unlimited' => 'boolean|nullable',
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function persist(DigitalProduct $product = null): RedirectResponse
    {
        if($product && $product -> exists()){
            $product -> autodelivery = $this -> autodelivery ?? false;
            $product -> unlimited = $this -> unlimited ?? false;
            // remove consecutive new lines and trim blank chars from start and end
            $formatedContent = trim(preg_replace("/[\r\n]{2,}/", "\n", $this -> product_content));
            $product -> content = $formatedContent;
            $product -> save();

            // update the quantity of products
            $product -> product -> quantity = !empty($formatedContent) ? substr_count($formatedContent, "\n") + 1 : 0;
            $product -> product -> save();


            session() -> flash('success', 'You have successfully changed digital options!');
            return redirect() -> back();
        }

        /** Creating a new DIGITAL PRODUCT **/

        $digitalProduct = session('product_details') ?? new DigitalProduct;
        if(!($digitalProduct instanceof DigitalProduct)){
            $digitalProduct = new DigitalProduct;
        }


        $digitalProduct -> autodelivery = $this -> autodelivery ?? false;
        $digitalProduct -> unlimited = $this -> unlimited ?? false;
        $digitalProduct -> setContent($this -> product_content);

        // update quantity if it is autodelivery
        if ($digitalProduct -> autodelivery) {
            $baseProduct = session() -> get('product_adding');
            if ($baseProduct) {
                // update quantity
                $baseProduct -> quantity = $digitalProduct -> newQuantity();
                // save it
                session() -> get('product_adding', $baseProduct);
            }
        }


        session() -> put('product_details', $digitalProduct);
        return redirect() -> route('profile.vendor.product.images');
    }
}
