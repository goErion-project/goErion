<?php

namespace App\Traits;

use App\Models\DigitalProduct;
use App\Models\PhysicalProduct;
use Webpatser\Uuid\Uuid;

/**
 * @method static creating(\Closure $param)
 */
trait Uuids
{
    /**
     * Boot function for the trait.
     */
    protected static function bootUuids(): void
    {
        static::creating(function ($model) {
            // Digital and physical products don't generate separate IDs
            // if the key is not already defined
            if (!($model instanceof PhysicalProduct) &&
                !($model instanceof DigitalProduct) &&
                is_null($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::generate()->string;
            }
        });
    }
}
