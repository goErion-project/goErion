<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $product_id
 * @property mixed|string $image
 * @property mixed|string $id
 * @property false|mixed $first
 * @method static find($id)
 */
class Image extends Model
{
    use Uuids;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Returns the product that holds this image
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this -> belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     *  Set the product for this image
     *
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this -> product_id = $product -> id;
    }
}
