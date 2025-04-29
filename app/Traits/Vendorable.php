<?php

namespace App\Traits;

use App\Models\Vendor as VendorModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method getId()
 * @method hasOne(string $class, string $string, string $string1)
 */
trait Vendorable
{
    public function isVendor()
    {
        return VendorModel::where('id',$this->getId())->exists();
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(VendorModel::class, 'id', 'id');
    }

    public function vendorSince(): string
    {
        return date_format($this->created_at, 'M/Y');
    }
}
