<?php

namespace App\Http\Requests\Product;

use App\Exceptions\RequestException;
use App\Marketplace\Utility\CurrencyConverter;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $min_quantity
 * @property mixed $price
 */
class NewOfferRequest extends FormRequest
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
            'min_quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric'
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RequestException
     * @throws \Exception
     */
    public function persist(Product $editingProduct = null): void
    {
        // offers for editing product
        if($editingProduct && $editingProduct -> exists()){
            $newOffer = new Offer();
            $newOffer -> min_quantity = $this -> min_quantity;
            $newOffer -> price = CurrencyConverter::convertToUsd($this -> price);
            $newOffer -> product_id = $editingProduct -> id;
            $newOffer -> save();

        }
        // offer for new product
        else{

            $offers = session('product_offers') ?? collect(); // return a collection of offers or empty collection

            // check if there is a same offer
            $minQuantity = $this -> min_quantity;
            if($offers -> search(function($offer, $key) use($minQuantity){ return $offer -> min_quantity == $minQuantity; }))
                throw new RequestException('There is already offer like that! Please remove old offer to add new one.');

            $newOffer = new Offer();
            $newOffer -> id = \Uuid::generate() -> string;
            $newOffer -> min_quantity = $this -> min_quantity;
            $newOffer -> price = CurrencyConverter::convertToUsd($this -> price);

            $offers -> push($newOffer); // put new offer
            $offers = $offers -> sortBy(function($offer){ return $offer -> min_quantity; });
            session(['product_offers' => $offers]); // save to session

        }
    }
}
