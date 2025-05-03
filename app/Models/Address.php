<?php

namespace App\Models;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $created_at
 */
/**
 * Represents the instance of the coin address for any user
 * Can be any Coin that is supported in the config
 *
 * Class Address
 * @property mixed $created_at
 * @property mixed $address
 * @property int|mixed $user_id
 * @property mixed|string $coin
 * @package App
 */
class Address extends Model
{
    use Uuids;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $incrementing = false;


    public static function label($coinName): string
    {
        if($coinName=='btcm')
            return 'btc pubkey';
        return $coinName;
    }


    /**
     * Relationship with the user
     */
    public function user(): BelongsTo
    {
        return $this -> belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Fix for Bitcoin multisig
     *
     * @param $coin
     * @return string
     */
    public function getCoinAttribute($coin): string
    {
        if($coin=='btcm')
            return 'btc pubkey';
        return $coin;
    }


    /**
     * String how long was passed since adding address
     *
     * @return string
     */
    public function getAddedAgoAttribute(): string
    {
        return Carbon::parse($this -> created_at) -> diffForHumans();
    }
}
