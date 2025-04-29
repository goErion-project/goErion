<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Vendor extends User
{
    use Uuids;
    protected $table = 'vendors';
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vendor_level', 'about','created_at', 'updated_at'];

    public static function allUsers(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        $vendorIDs = Vendor::all()->pluck('id');
        return User::query()->whereIn('id', $vendorIDs)->get();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'id');
    }
}
