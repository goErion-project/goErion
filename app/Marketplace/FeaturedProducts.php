<?php


namespace App\Marketplace;


class FeaturedProducts
{

    protected static string $moduleName = 'FeaturedProducts';

    public static function isEnabled(): bool {
        return ModuleManager::isEnabled(self::$moduleName);
    }

    public static function get(){
        try{
            $featuredStatus = resolve('Modules\FeaturedProducts\main\FeaturedStatus');
            // dd($featuredStatus);
            $featuredProducts = $featuredStatus->getFeaturedProducts();
        } catch(\Exception $e){
            dd($e->getMessage());
            $featuredProducts = null;
        }

        return $featuredProducts;
    }
}
