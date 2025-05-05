<?php

namespace App\Marketplace;


use Nwidart\Modules\Facades\Module;

class ModuleManager
{
    private static array $availableModules =
        [
            'MultiCurrency',
            'FinalizeEarly',
            'FeaturedProducts'
        ];

    public static function isEnabled($module): bool
    {
        if (!in_array($module, self::$availableModules))
        {
            return false;
        }
        if (!Module::has($module))
        {
            return false;
        }
        if (!Module::isEnabled($module))
        {
            return false;
        }
        return true;
    }
}
