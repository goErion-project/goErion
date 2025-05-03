<?php

namespace App\Models;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $coin
 * @property mixed $address
 * @property mixed $created_at
 */
class VendorPurchase extends Model
{
    use Uuids;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $incrementing = false;

    /**
     * Relationship with the user
     */
    public function user(): BelongsTo
    {
        return $this -> belongsTo(User::class);
    }


    /**
     * Returns the balance of the deposit address
     *
     * @return float
     * @throws \Exception
     */
    public function getBalance(): float
    {
        $coinServiceClass = config('coins.coin_list.'. $this -> coin);
        $coinService = new $coinServiceClass();

        // Send request for balance of the address
        return  $coinService->getBalance(['address' => $this -> address]);
    }

    /**
     * Return a formatted number of balances of the address
     *
     * @return string
     */
    public function getBalanceAttribute(): string
    {
        try {
            $balanceAddress = $this -> getBalance();
        }
        catch (\Exception $e){
            // inform admin
            Log::warning("Request for balance of the '$this->address', coin '$this->coin' is failed because:");
            Log::warning($e -> getMessage());
            return 'Unavailable';
        }
        return number_format( $balanceAddress,8);
    }


    /**
     * Returns how much need to be paid to the address
     *
     * @return float
     */
    public function getTargetBalance(): float
    {
        $coinServiceClass = config('coins.coin_list.'. $this -> coin);
        $coinService = new $coinServiceClass();

        $vendorFeeUsd = config('marketplace.vendor_fee');
        return  $coinService -> usdToCoin($vendorFeeUsd);
    }

    /**
     * Returns formated number how much needs to be paid
     *
     * @return string
     */
    public function getTargetAttribute(): string
    {
        return number_format($this -> getTargetBalance(), 8);
    }

    /**
     *  Returns true if there are enough funds on this coin address
     *
     *  @return bool
     */
    public function isEnough(): bool
    {
        try{
            $coinServiceClass = config('coins.coin_list.'. $this -> coin);
            $coinService = new $coinServiceClass();

            $vendorFeeUsd = config('marketplace.vendor_fee');
            $vendorFeeCoin = $coinService -> usdToCoin($vendorFeeUsd);

            // returns true if the balance is bigger than it should be paid
            return $this -> getBalance() >= $vendorFeeCoin;
        }
        catch (\Exception $e){
            Log::warning($e -> getMessage());
            return false;
        }

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


    /**
     * Unload all funds to the market address
     *
     * @return bool
     */
    public function unloadFunds(): bool
    {
        try{
            $coinServiceClass = config('coins.coin_list.'. $this -> coin);
            $coinService = new $coinServiceClass();

            $marketCoinAddresses = config('coins.market_addresses.' . $this->coin);
            // pick one from the array
            $marketCoinAddress = $marketCoinAddresses[array_rand($marketCoinAddresses)];

            // send it to market address
            $coinService->sendToAddress($marketCoinAddress, $this->getBalance());

        }
        catch (\Exception $e){
            Log::warning($e);
            return false;
        }
    }
}
