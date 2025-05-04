<?php

namespace App\Marketplace\Utility;

use Illuminate\Support\Facades\Cache;

/**
 * Class for bitcoin convert to usd and backwards
 *
 * Class BitcoinConverter
 * @package App\Marketplace\Utility
 */

class BitcoinConverter
{
    /**
     * Returns the float amount of the usd
     *
     * @param float $amount
     * @return float
     */
    public static function usdToBtc(float $amount): float
    {
        $btcprice = Cache::remember('btc_price',config('coins.caching_price_interval'),function(){
            // get bitcoin price
            $url = "https://www.bitstamp.net/api/ticker/";
            $json = json_decode(file_get_contents($url), true);
            return $json["last"];
        });
        // calculate bitcoins and store
        return $amount / $btcprice;


    }


    /**
     * Returns the amount of btc converted from usd
     *
     * @param float $amount
     * @return float
     */
    public static function btcToUsd(float $amount) : float
    {
        $btcprice = Cache::remember('btc_price',config('coins.caching_price_interval'),function(){
            // get bitcoin price
            $url = "https://www.bitstamp.net/api/ticker/";
            $json = json_decode(file_get_contents($url), true);
            return $json["last"];
        });

        // calculate bitcoins and store
        return $amount * $btcprice;
    }

    public static function satoshiToBtc(int $satoshi) : float
    {
        return $satoshi / 100000000;
    }
}
