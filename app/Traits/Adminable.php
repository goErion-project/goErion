<?php

namespace App\Traits;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method hasOne(string $class, string $string, string $string1)
 */
trait Adminable
{
    public function isAdmin(): bool
    {
        return Admin::where('id',$this->id)->exists();
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'id', 'id');
    }
}
