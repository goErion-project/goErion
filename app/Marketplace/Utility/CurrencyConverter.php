<?php

namespace App\Marketplace\Utility;

use App\Marketplace\ModuleManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Intl;

/**
 * @method static
 */
class CurrencyConverter
{
    protected static string $moduleName = 'MultiCurrency';

    /**
     * Converts USD value to local value
     */
    public static function convert($usdValue, $localValue = 'usd') {
        if (!self::isEnabled()) {
            return $usdValue;
        }

        $converter = resolve('MultiCurrencyModule\Converter');
        return round( $converter->convert($usdValue, $localValue), 2, PHP_ROUND_HALF_EVEN);

    }

    /**
     * Converts local value to USD value
     *
     * @param $localAmount
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function convertToUsd($localAmount): string
    {
        if (!self::isEnabled()) {
            return $localAmount;
        }

        $converter = resolve('MultiCurrencyModule\Converter');
        return round( $converter->convertFromLocal($localAmount, CurrencyConverter::getLocalCurrency()), 2, PHP_ROUND_HALF_EVEN);

    }

    public static function getSymbol($localValue = 'USD'): string {
        if ($localValue == 'USD') {
            return '$';
        }

        return Currencies::getSymbol($localValue);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getLocalCurrency() {
        if (!self::isEnabled()){
            return 'USD';
        }
        $user = auth()->user();
        if ($user == null) {
            return 'USD';
        }
        if (session()->has('local_currency')){
            return session()->get('local_currency');
        }
        session()->put('local_currency',$user->local_currency);
        return session()->get('local_currency');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function convertToLocal($usdValue) {

        return self::convert($usdValue, self::getLocalCurrency());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getLocalSymbol(): string
    {
        return self::getSymbol(self::getLocalCurrency());
    }

    public static function isEnabled(): bool {

        return ModuleManager::isEnabled(self::$moduleName);
    }

    public static function getSupportedCurrencies() {
        if (!self::isEnabled()) {
            return [];
        }
        $converter = resolve('MultiCurrencyModule\Converter');

        return $converter->getSupportedCurrencies();
    }
}
