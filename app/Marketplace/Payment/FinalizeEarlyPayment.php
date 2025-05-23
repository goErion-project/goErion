<?php


namespace App\Marketplace\Payment;


use App\Marketplace\ModuleManager;

class FinalizeEarlyPayment
{
    public static string $moduleName = 'FinalizeEarly';
    public static string $shortName = 'fe';

    public static function isEnabled(): bool {

        return ModuleManager::isEnabled(self::$moduleName);
    }

    public static function getProcedure(){
        if (!self::isEnabled())
            return null;
        return resolve('FinalizeEarlyModule\Procedure');
    }
}
