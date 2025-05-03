<?php

namespace App\Marketplace\Payment;

use App\Exceptions\RequestException;
use App\Models\Purchase;

abstract class Payment
{
    /**
     * Mapping (string)$coinName to a Coin object
     *
     * @param $coinName
     * @return Coin
     * @throws \Exception
     */
    public static function coinService($coinName): Coin
    {
        try {
            $coinClass = config('coins.coin_list')[$coinName];
        }
            // transform exception
        catch (\Exception $e){
            throw new \Exception('Coin "' . $coinName . '" is not supported!');
        }

        // create an instance of the coin service
        return new $coinClass;
    }

    /**
     * Instance of the Class that communicates with coin
     *
     * @var Coin
     */
    protected Coin $coin;



    /**
     * Instance of the purchase that runs this procedure
     *
     * @var Purchase
     */
    protected Purchase $purchase;

    /**
     * @throws \Throwable
     */
    public function __construct(Purchase $purchase)
    {
        throw_if(!class_exists(BitcoinMutlisig::class) && $purchase->coin_name == 'btcm',
            new RequestException('The Multisig is not supported!'));

        // Set the target purchase in the constructor
        $this -> purchase = $purchase;

        // make an instance of coin service
        $this -> coin = self::coinService($purchase->coin_name);
    }


    abstract public function purchased();

    abstract public function sent();

    abstract public function delivered();

    abstract public function canceled();

    /**
     * in parameters key 'receiving_address' must be defined to send to this address
     *
     * @param array $parameters
     * @return mixed
     */
    abstract public function resolved( array $parameters ): mixed;

    /**
     * Returns amount to pay
     *
     * @return float
     */
    abstract function balance() : float;


    /**
     * Converts from USD amount to equivalent coin amount
     *
     * @param $usd
     * @return float
     */
    abstract function usdToCoin( $usd ) : float;


    /**
     * Returns label of this coin
     *
     * @return string
     */
    abstract function coinLabel() : string;
}
