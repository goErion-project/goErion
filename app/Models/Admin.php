<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Admin extends User
{
    use Uuids;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    public static function allUsers(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        //select all admins ids
        $adminsIDs = Admin::all()->pluck('id');
        return User::query()->whereIn('id', $adminsIDs)->get();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'id');
    }
}
