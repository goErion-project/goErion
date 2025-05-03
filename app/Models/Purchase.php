<?php

namespace App\Models;

use App\Marketplace\Payment\Payment;
use App\Marketplace\PGP;
use App\Marketplace\Utility\CurrencyConverter;
use App\Marketplace\Utility\UUID;
use App\Traits\DisplayablePurchase;
use App\Traits\Purchasable;
use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed|string $status
 * @property mixed $receiving_address
 * @property mixed $amount_due
 * @property $coin_name
 * @property mixed $id
 * @property mixed|string $offer_id
 * @property mixed $shipping_id
 * @property int|mixed $buyer_id
 * @property int|mixed $vendor_id
 * @property mixed $shipping
 * @property mixed $offer
 * @property mixed $quantity
 * @property float $to_pay
 * @property $message
 * @property mixed $created_at
 * @property mixed $vendor
 * @property mixed $buyer
 * @property string $state
 * @property mixed $feedback_id
 * @property mixed $type
 */
class Purchase extends Model
{
    use Uuids;
    use DisplayablePurchase;
    use Purchasable;
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Types of purchases
     *
     * @var array
     */
    public static array $types = [
        'fe' => 'Finalize Early',
        'normal' => 'Normal Escrow',
//        'multisig' => 'Multisignature',
    ];
    const DEFAULT_TYPE = 'normal';

    /**
     * State of the purchases
     *
     * @var array
     */
    public static array $states = [
        'purchased' => 'Purchased',
        'sent' => 'Sent',
        'delivered' => 'Delivered',
        'disputed' => 'Disputed',
        'canceled' => 'Canceled'
    ];
    const DEFAULT_STATE = 'purchased';

    /**
     * Transforms coin display names
     *
     * @param $coinName
     * @return string
     */
    public static function coinDisplayName($coinName): string
    {
        if($coinName=='btcm')
            return 'btc multisig';
        return $coinName;
    }

    public static function totalEarningPerCoin() : array
    {
        $total_spent = 0;
        $total_earnings_coin = [];
        foreach (Purchase::query()->where('state', 'delivered') -> get()  as $deliveredPurchase){
            $total_spent += $deliveredPurchase->getSumDollars();

            // sum up earning per coin
            if(!array_key_exists($deliveredPurchase->coin, $total_earnings_coin)){
                $total_earnings_coin[$deliveredPurchase->coin_name] = $deliveredPurchase->to_pay;
            }
            // add up for the coin
            else{
                $total_earnings_coin[$deliveredPurchase->coin_name] += $deliveredPurchase->to_pay;
            }
        }

        return $total_earnings_coin;
    }

    public static function totalSpent() : float
    {
        $total_spent = 0;
        foreach (Purchase::query()->where('state', 'delivered') -> get()  as $deliveredPurchase){
            $total_spent += $deliveredPurchase->getSumDollars();

        }
        return $total_spent;
    }

    /**
     * Service Payment class
     *
     *
     */
    private $payment;

    /**
     * Lazy loading of payment
     *
     * @throws BindingResolutionException
     */
    public function getPayment()
    {
        if($this -> payment == null)
            $this -> payment = app() -> makeWith(Payment::class, ['purchase' => $this]);
        return $this -> payment;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return short id of the purchase
     *
     * @return bool|string
     */
    public function getShortIdAttribute(): bool|string
    {

        return UUID::encode($this->id);
    }

    /**
     * Set offer for the purchase
     *
     * @param Offer $offer
     */
    public function setOffer(Offer $offer): void
    {
        $this -> offer_id = $offer -> id;
    }

    /**
     * Set shipping of the purchase
     *
     * @param Shipping|null $shipping
     */
    public function setShipping(?Shipping $shipping): void
    {
        if(!is_null($shipping))
            $this -> shipping_id = $shipping -> id;
    }

    /**
     * Set buyer of the purchase
     *
     * @param User $user
     */
    public function setBuyer(User $user): void
    {
        $this -> buyer_id = $user -> id;
    }

    /**
     * Display the name of the purchase coin
     *
     * @param $coin
     * @return string
     */
    public function getCoinAttribute($coin): string
    {
        return self::coinDisplayName($coin);
    }

    /**
     * Set the vendor of the purchase
     *
     * @param Vendor $vendor
     */
    public function setVendor(Vendor $vendor): void
    {
        $this -> vendor_id = $vendor -> id;
    }

    /**
     * Get offer of the purchase
     *
     * @return HasOne
     */
    public function offer(): HasOne
    {
        return $this -> hasOne(Offer::class, 'id', 'offer_id');
    }

    /**
     * Get Shipping of the purchase can be null
     *
     * @return HasOne
     */
    public function shipping(): HasOne
    {
        return $this -> hasOne(Shipping::class, 'id', 'shipping_id');
    }

    /**
     * Get a buyer of the purchase
     *
     * @return HasOne
     */
    public function buyer(): HasOne
    {
        return $this -> hasOne(User::class, 'id', 'buyer_id');
    }

    /**
     * Get vendor of the purchase
     *
     * @return HasOne
     */
    public function vendor(): HasOne
    {
        return $this -> hasOne(Vendor::class, 'id', 'vendor_id');
    }

    /**
     * Returns the sum of dollars that needs to be paid for this purchase
     *
     * @return float|int
     */
    public function getSumDollars(): float|int
    {
        $shipingPrice = 0;
        if($this -> shipping)
            $shipingPrice += $this -> shipping -> price;
        return $this -> offer -> price * $this -> quantity + $shipingPrice;
    }


    /**
     * Returns a sum that needs to be paid in local currency
     *
     */
    public function getSumLocalCurrency(){
        try {
            return CurrencyConverter::convertToLocal($this->getSumDollars());
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {

        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLocalSymbol(): string
    {
        return CurrencyConverter::getSymbol(CurrencyConverter::getLocalCurrency());
    }

    /**
     * Sum of the purchase
     *
     * @return float|int
     */
    public function getValueSumAttribute(): float|int
    {
        return $this -> getSumDollars();
    }

    /**
     * Sum of Coin needed to be paid
     *
     * @return float
     */
    public function getSum(): float
    {
        return $this -> to_pay;
    }

    /**
     * Formated Coin sum needed to be paid
     *
     * @return string
     */
    public function getCoinSumAttribute(): string
    {
        return number_format($this -> getSum(), 8);
    }

    /**
     * Encrypt a message with the vendors PGP key
     * @throws \Exception
     */
    private function encryptMessage(): void
    {
        // If the message is not already encrypted
        if($this -> message && !Message::messageEncrypted($this -> message)){
            $this -> message = PGP::EncryptMessage($this -> message, $this -> vendor -> user -> pgp_key);
        }
    }

    /**
     * Time difference from purchased time to now
     *
     * @return string
     */
    public function timeDiff(): string
    {
        return Carbon::parse($this -> created_at) -> diffForHumans();
    }

    public function setCoin($coinName): void
    {
        $this -> coin_name = $coinName;
    }



    /**
     * Returns if the logged user is allowed to see
     *
     * @return bool
     */
    public function isAllowed() : bool
    {
        // return true for user and buyer
        return auth() -> check() && // must be logged in
            (  auth() -> user() == $this -> vendor -> user // user is vendor of the sale
                || auth() -> user() == $this -> buyer // user is buyer of the sale
                || auth() -> user() -> isAdmin() ); // user is admin
    }

    /**
     * Returns true if logged user is vendor for this purchase
     *
     * @param User|null $user
     * @return bool
     */
    public function isVendor(User $user = null) : bool
    {
        // Compare id if the user is given
        if(!is_null($user))
            return $this -> vendor_id == $user -> id;
        // otherwise check logged user
        return auth() -> check() && auth() -> user() -> id == $this -> vendor_id;
    }

    /**
     * Returns true if the user is a buyer
     *
     * @param User|null $user
     * @return bool
     */
    public function isBuyer(User $user = null) : bool
    {
        // if user is set than check if given user is a buyer
        if(!is_null($user))
            return $this -> buyer == $user;
        // otherwise check if logged user
        return auth() -> check() && auth() -> user() == $this -> buyer;
    }

    /**
     * Returns true if the purchase is purchased
     *
     * @return bool
     */
    public function isPurchased(): bool
    {
        return $this -> state == 'purchased';
    }

    /**
     * Returns true if the purchase is sent state
     *
     * @return bool
     */
    public function isSent(): bool
    {
        return $this -> state == 'sent';
    }

    /**
     * Returns true if the purchase is delivered state
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this -> state == 'delivered';
    }

    /**
     * Returns true if the purchase is disputed state
     *
     * @return bool
     */
    public function isDisputed(): bool
    {
        return $this -> state == 'disputed' && Dispute::where('id', $this -> dispute_id) -> exists();
    }

    /**
     * Returns true if the state of the purchase is canceled
     *
     *
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->state == 'canceled';
    }

    /**
     * Set Dispute of the purchase
     *
     * @param Dispute $dispute
     * @return mixed
     */
    public function setDispute(Dispute $dispute)
    {
        return $this -> dispute_id = $dispute -> id;
    }

    /**
     * Return \App\Dispute
     *
     * @return HasOne
     */
    public function dispute()
    {
        return $this -> hasOne(\App\Dispute::class, 'id', 'dispute_id');
    }

    /**
     * Defines if user can make dispute on this purchase
     *
     * @return bool
     */
    public function canMakeDispute() : bool
    {
        if(!auth() -> check()) return false;
        if($this -> isBuyer() || $this -> isVendor()) return true;

        return false;
    }

    /**
     * Return the name of the user role in this purchase
     *
     * @param User $user
     * @return string
     */
    public function userRole(User $user) : string
    {
        if($user -> id == $this -> vendor_id) return '(vendor)';
        if($user -> id == $this -> buyer_id) return '(buyer)';

        return '';
    }


    /**
     * Returns feedback of the purchase
     *
     * @return HasOne
     */
    public function feedback(): HasOne
    {
        return $this -> hasOne(Feedback::class, 'id', 'feedback_id');
    }

    /**
     * Returns if this purchase has feedback
     *
     * @return bool
     */
    public function hasFeedback(): bool
    {
        return $this -> feedback_id != null;
    }

    /**
     * Setter for feedback
     *
     * @param Feedback $feedback
     */
    public function setFeedback(Feedback $feedback): void
    {
        $this -> feedback_id = $feedback -> id;
    }

    /**
     * Returns the balance of the addresses
     *
     * @return float
     */
    public function getBalance() : float
    {
        $addressBalance = 0;
        // Catch errors
        try{
            $addressBalance = $this -> getPayment() -> balance();
        }
        catch (\Exception $e){
            // Inform logger
            Log::error($e);
            $addressBalance = -1;
        }

        return $addressBalance;
    }

    /**
     * Returns if there are enough balances on the address
     *
     * @return bool
     */
    public function enoughBalance() : bool
    {
        // returns true if the balance on the deposit addresses greater or equal than enough btc sumo
        return $this->getBalance() >= $this -> getSum();
    }

    /**
     * Getter for Coin balance, formated string with 8 decimals
     *
     * @return float|string
     */
    public function getCoinBalanceAttribute(): float|string
    {
        $balance = $this -> getBalance();
        if($balance == -1)
            return 'unavailable';
        return number_format($balance, 8);
    }

    /**
     * Get coin label
     *
     * @return string
     * @throws BindingResolutionException
     */
    public function getCoinLabelAttribute(): string
    {
        return strtoupper(self::coinDisplayName($this -> getPayment() -> coinLabel()));
    }

}
