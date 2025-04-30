<?php

namespace App\Models;

use App\Marketplace\Payment\FinalizeEarlyPayment;
use App\Traits\Experience;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @property array|mixed|null $profilebg
 * @property mixed $can_use_fe
 */
class Vendor extends User
{
    use Uuids;
    use Experience;


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

    public function getProfileBg()
    {
        if ($this->profilebg == null)
        {
            $this->profilebg = Arr::random(config('vendor.profile_bgs'));
            $this->save();
        }
        return $this->profilebg;
    }

    public function getId(){
        return $this->id;
    }

    public function canUseFe(): bool
    {
        return $this->can_use_fe == 1 && FinalizeEarlyPayment::isEnabled();
    }
}
