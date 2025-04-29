<?php

namespace App\Marketplace;

class FeaturedProducts
{
    private static string $moduleName = 'FeaturedProducts';

    public static function isEnabled(): bool
    {
        return ModuleManager::isEnabled(self::$moduleName);
    }

    public static function get(): string
    {
        try {
            $featuredStatus = resolve('FeaturedProductsModule\Status');
            $featuredProducts = $featuredStatus->getFeaturedProducts();
        }catch (\Exception $e)
        {
            $featuredProducts = null;
        }
        return $featuredProducts;
    }
}
