<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Wishlist extends Model
{
    protected $fillable = ['product_id', 'user_id'];
    /**
     * Returns if the whishlist existing with product and user
     *
     * @param Product $product
     * @param User $user
     * @return bool
     */
    public static function added(Product $product, User $user): bool
    {
        return self::query()->where('product_id', $product -> id) -> where('user_id', $user -> id) -> exists();
    }

    /**
     * Returns the collectionWishlist if the logged user is a favorite product
     *
     * @param Product $product
     * @return Collection|null
     */
    public static function getWish(Product $product): ?Collection
    {
        if(auth() -> check())
            return self::query()->where('product_id', $product -> id) -> where('user_id', auth() -> user() -> id);
        return null;
    }

    /**
     * Return \App\User
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this -> hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Returns \App\Product
     *
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this -> hasOne(Product::class,'id', 'product_id');
    }
}
