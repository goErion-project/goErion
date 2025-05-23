<?php

namespace App\Marketplace;


use App\Exceptions\RequestException;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Shipping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Singleton class that manages to add, editing and removing products from cart
 *
 * Class Cart
 * @package App\Marketplace
 *
 */
class Cart
{
    const SESSION_NAME = 'cart_items';

    /**
     * Static instance of the cart
     *
     *
     */
    private static ?Cart $cart = null;

    public static function getCart() : Cart
    {
        if (self::$cart === null) {
            self::$cart = new Cart();
        }
        return self::$cart;
    }

    /**
     * Private constructor because it is a singleton
     *
     * Cart constructor.
     */
    private function __construct(){}


    /**
     * Validate shipping for appropriate quantity
     *
     * @param Shipping|null $shipping
     * @param int $quantity
     * @return bool
     */
    private function validShipping(?Shipping $shipping, int $quantity) : bool
    {
        if($shipping == null)
            return true;
        return ($shipping -> from_quantity <= $quantity && $shipping -> to_quantity >= $quantity);
    }

    /**
     * Check if there is a valid offer
     *
     * @param Product $product
     * @param int $quantity
     * @return bool
     */
    private function validOffer(Product $product, int $quantity) : bool
    {
        return $product -> offers -> where('min_quantity', '<=', $quantity ) -> isNotEmpty();
    }


    /**
     * Add product to the cart with quantity and shipping
     *
     * @param Product $product
     * @param int $quantity
     * @param string $coin
     * @param Shipping|null $shipping
     * @param string|null $message
     * @param string $type
     * @throws RequestException
     * @throws \Exception
     */
    public function addToCart(Product $product, int $quantity, string $coin, ?Shipping $shipping = null, string $message = null, $type = 'normal'): void
    {
        // validate shipping
        if(!$this -> validShipping($shipping, $quantity))
            throw new RequestException('Quantity must be in range of selected shipping option!');
        // validate offers
        if(!$this -> validOffer($product, $quantity))
            throw new RequestException('There is no offer for this quantity!');
        // validate coins
        if(!$product -> supportsCoin($coin))
            throw new RequestException('Selected coin payment is not supported by this product!');

        // make the purchase
        $newCartItem = new Purchase;
        $newCartItem -> id =  \Uuid::generate() -> string; // generate id for address
        $newCartItem -> setBuyer(auth() -> user());
        $newCartItem -> setVendor($product -> user -> vendor);
        $newCartItem -> setOffer($product -> bestOffer($quantity));
        $newCartItem -> setShipping($shipping);
        $newCartItem -> message = $message ?? '';
        $newCartItem -> quantity = $quantity;
        $newCartItem -> coin_name = $coin;
        $newCartItem -> type = $type;
        /**
         * Cart table
         * \App\Product -> id => \App\Purchase
         */
        // get cart table from session
        $itemsTable = session(self::SESSION_NAME);
        // put as the key of the product
        $itemsTable[$product -> id] = $newCartItem;
        // save to table
        session() -> put(self::SESSION_NAME, $itemsTable);

    }

    /**
     * Array of items
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function items(): mixed
    {
        return session() -> get(self::SESSION_NAME) ?? [];
    }


    /**
     * Remove product from cart
     *
     * @param Product $product
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function removeFromCart(Product $product): void
    {
        // get from session
        $cartItems = session() -> get(self::SESSION_NAME);

        unset($cartItems[$product -> id]);

        session() -> put(self::SESSION_NAME, $cartItems);
    }

    /**
     * Number of items in the cart
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function numberOfItems(): int
    {

        if (session() -> get(self::SESSION_NAME) == null)
            return 0;
        return count(session() -> get(self::SESSION_NAME));
    }

    /**
     * Total sum of dollars
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function total(): int
    {
        $totalSum = 0;

        foreach (session() -> get(self::SESSION_NAME) ?? [] as $productId => $item){
            $totalSum += $item -> value_sum;
        }

        return $totalSum;
    }

    /**
     * Clear cart session
     */
    public function clearCart(): void
    {
        session() -> forget(self::SESSION_NAME);
    }
}
