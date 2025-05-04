<?php

namespace App\Marketplace\Utility;

class MoneroConvert
{
    const API_URL = "https://min-api.cryptocompare.com/data/price?fsym=XMR&tsyms=EUR";
    const API_URL_USD = "https://min-api.cryptocompare.com/data/price?fsym=XMR&tsyms=USD";
    const EUR = "EUR";
    const USD = "USD";
    const XMR_TO_PICONERO = 1000000000000;

    public static function xmrToEur(float $amount): float
    {
        $rate = file_get_contents(self::API_URL);
        $euroRate = json_decode($rate)->EUR;
        return $amount * $euroRate;
    }

    public static function xmrToUsd(float $amount): float
    {
        $rate = file_get_contents(self::API_URL_USD);
        $usdRate = json_decode($rate)->USD;
        return $amount * $usdRate;
    }

    public static function eurToXmr(float $amount): float
    {
        $rate = file_get_contents(self::API_URL);
        $euroRate = json_decode($rate)->EUR;
        return $amount / $euroRate;
    }

    public static function usdToXmr(float $amount): float
    {
        $rate = file_get_contents(self::API_URL_USD);
        $euroRate = json_decode($rate)->USD;
        return $amount / $euroRate;
    }
    public static function toPicoNero($xmr): float|int
    {
        return $xmr * self::XMR_TO_PICONERO;
    }
    public static function toXmr($piconero): float|int
    {
        return $piconero / self::XMR_TO_PICONERO;
    }
    public static function piconeroToEur($piconero): float
    {
        return self::xmrToEur( self::toXmr($piconero));
    }

    public static function piconeroToUsd($piconero): float
    {
        return self::xmrToUsd( self::toXmr($piconero));
    }
}
