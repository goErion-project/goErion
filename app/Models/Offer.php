<?php

namespace App\Models;

use App\Marketplace\Utility\CurrencyConverter;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @method static avg(string $string)
 * @property mixed $price
 * @property mixed $min_quantity
 * @property mixed $product_id
 * @property mixed|string $id
 */
class Offer extends Model
{
    use Uuids;
    use SoftDeletes;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public static function averagePrice() : float
    {
        $averagePrice = self::avg('price');
        if($averagePrice != null)
        {
            return $averagePrice;
        }
        return 0;
    }

    /**
     * Relationship with the product
     *
     *
     */
    public function product(): BelongsTo
    {
        return $this -> belongsTo(Product::class);
    }

    /**
     * @return string number of dollars with 2 decimals
     */
    public function getDollarsAttribute(): string
    {
        return number_format($this -> price, 2, '.', '');
    }



    /**
     * Relationship of purchases
     *
     * @return HasMany
     */
//    public function purchases(): HasMany
//    {
//        return $this -> hasMany(\App\Purchase::class, 'offer_id', 'id');
//    }

    /**
     * Converts price of the form to local price
     *
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLocalPriceAttribute(): string
    {
        return CurrencyConverter::convertToLocal($this->price);
    }
}
