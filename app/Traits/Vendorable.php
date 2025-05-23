<?php

namespace App\Traits;

use App\Exceptions\RequestException;
use App\Models\Vendor as VendorModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @method getId()
 * @method hasOne(string $class, string $string, string $string1)
 */
trait Vendorable
{
    /**
     * Returns true if the user is a vendor
     *
     * @return bool
     */
    public function isVendor(): bool
    {
        return VendorModel::where('id', $this -> getId()) -> exists();
    }

    /**
     * Return Vendor instance of the user
     *
     * @return HasOne
     */
    public function vendor(): HasOne
    {
        return $this -> hasOne(VendorModel::class, 'id', 'id');
    }

    /**
     * Returns true if the user paid to one of the deposit addresses
     *
     * @return bool
     */
    private function depositedEngouh(): bool
    {
        foreach ($this -> vendorPurchases as $depositAddress){
            if($depositAddress -> isEnough()){
                return true;
            }
        }
        return false;
    }

    /**
     * Creates an instance of the Vendor from a user
     *
     * @throws RequestException
     * @throws \Throwable
     */
    public function becomeVendor($address = null): void
    {
        if(!$this -> hasPGP())
            throw new RequestException('You can\'t become vendor if you don\'t have PGP key!');

        // Vendor must have addresses of each coin
//        foreach (array_keys(config('coins.coin_list')) as $coinName){
//            // if the coin doesnt exists
//            if(!$this -> addresses() -> where('coin', $coinName) -> exists())
//                throw new RedirectException("You need to have '" . strtoupper($coinName) . "' address in your account to become vendor!", route('profile.index'));
//        }
        // check if the user deposited address
        throw_unless($this -> depositedEngouh(), new RequestException("You must deposit enough funds to the one address!"));

        try{
            DB::beginTransaction();

            // update balances of the vendor purchases
            foreach ($this -> vendorPurchases as $depositAddress){
                $depositAddress->amount = $depositAddress->getBalance();

                // Unload funds to market address
                if($depositAddress->getBalance()>0)
                    $depositAddress->unloadFunds();

                $depositAddress->save();
            }


            VendorModel::insert([
                'id' => $this -> getId(),
                'vendor_level' => 0,
                'about' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new RequestException("Error happened! Try again later!");
        }
    }


    /**
     * Methods required to display vendor statistics on profile
     */

    public function vendorSince(): string
    {
        return date_format($this->created_at,"M/Y");
    }


    public function completedOrders(): int
    {
        return $this->sales()->where('state','delivered')->count();
    }

    public function disputesLastYear($won = true,$months =12): int
    {
        $vendorID = $this->getId();
        return $this->sales()->whereHas('dispute',function ($query) use ($vendorID,$won,$months){
            $operator = '=';
            if (!$won){
                $operator = '!=';
            }
            $query->where('winner_id',$operator,$vendorID)->where("created_at",">", Carbon::now()->subMonths($months));
        })->count();
    }

}
