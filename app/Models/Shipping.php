<?php

namespace App\Models;

use App\Marketplace\Utility\CurrencyConverter;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $price
 * @property mixed $name
 * @property mixed $duration
 * @property mixed $product_id
 * @property mixed $from_quantity
 * @property mixed $to_quantity
 */
class Shipping extends Model
{
    use Uuids;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * Returns PhysicalProduct that belongs to this shipping
     *
     * @return HasOne
     */
    public function physicalProduct(): HasOne
    {
        return $this -> hasOne(PhysicalProduct::class, 'id', 'product_id');
    }


    /**
     * @return string number of dollars with 2 decimals
     */
    public function getDollarsAttribute(): string
    {
        return number_format($this -> price, 2, '.', '');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLongNameAttribute(): string
    {
        return $this -> name . ' - ' . $this -> duration . ' - ' .  CurrencyConverter::convertToLocal($this -> price). ' '. CurrencyConverter::getSymbol(CurrencyConverter::getLocalCurrency()).  ' (' . $this -> from_quantity  .  ' - ' .  $this -> to_quantity . ' ' . $this -> physicalProduct -> product -> mesure .  ' )';
    }

    /**
     * Set the product for a new model
     *
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        if($product != null)
            $this -> product_id = $product -> id;
    }

    /**
     * Returns 2 decimals stringed number of local value of the shipping
     *
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLocalValueAttribute(): string
    {
        return number_format(CurrencyConverter::convertToLocal($this -> price), 2, '.', '');
    }
}
