<?php

namespace App\Traits;

use Exception;
use Webpatser\Uuid\Uuid;

/**
 * @method static creating(\Closure $param)
 */
trait Uuids
{
    protected static function boot()
    {
        parent::boot();

        static::creating(
        /**
         * @throws Exception
         */ function ($model){
            if (is_null($model->{$model->getKeyName()})){
                $model->{$model->getKeyName()} = Uuid::generate()->string;
            }
        });


    }
}
