<?php

namespace App\Models;

use App\Exceptions\RequestException;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $content
 * @property mixed $product
 * @property false|mixed $autodelivery
 * @property false|mixed $unlimited
 */
class DigitalProduct extends User
{
    use Uuids;

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'id');
    }

    public function shippings(): null
    {
        return null;
    }

    public function setContent(?string $newContent): void
    {
        $newContent = empty($newContent) ? null : $newContent;
        //remove consecutive new lines and trim balnk chars
        $formatedContent = trim(preg_replace('/[\r\n]{2,}/', "\n", $newContent));
        $this->content = $formatedContent;
    }

    public function newQuantity(): int
    {
        return !empty($this->content) ? substr_count($this->content,"\n") + 1 : 0;
    }

    /**
     * @throws RequestException
     */
    public function getProducts(int $quantity): array
    {
        if ($quantity > $this->newQuantity())
            throw new RequestException('There is not enough products in the stock.');
        $productsToDelivery = [];
        //push to products from product content
        while ($quantity > 0)
        {
            //extract the first product
            $firstProduct = substr($this->content, 0, strpos($this->content, "\n"));

            //push product to array
            $productsToDelivery[] = $firstProduct;
            //remove first product
            $this->content = substr($this->content, strpos($this->content, "\n") + 1);

            $quantity--;
        }
        $this->save();
        //update App\Model\Product quantity
        $this->product ->updateQuantity();
        $this->product->save();

        return $productsToDelivery;
    }
}
