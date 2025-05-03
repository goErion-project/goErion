<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the product.
     *
     * @param User|null $user
     * @param Product $product
     * @return false
     */
    public function view(?User $user, Product $product): false
    {
        return $product -> active;
    }

    /**
     * Determine whether the user can create products.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user -> isVendor();
    }

    /**
     * Determine whether the user can update the product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return bool
     */
    public function update(User $user, Product $product): bool
    {
        // product can be updated by the owner or by the admin/moderator
        return ($product -> user == $user || $user -> isAdmin() || $user -> hasPermission('products')) && $product -> active;
    }

    /**
     * Determine whether the user can delete the product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return false
     */
    public function delete(User $user, Product $product): false
    {
        return false; // forbid deleting
    }

    /**
     * Determine whether the user can restore the product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return void
     */
    public function restore(User $user, Product $product)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return void
     */
    public function forceDelete(User $user, Product $product)
    {
        //
    }
}
