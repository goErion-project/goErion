<?php

namespace App\Models;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property mixed $product_id
 * @property int|mixed $vendor_id
 * @property int|mixed $buyer_id
 * @property mixed $type
 * @property mixed $buyer
 * @property mixed $created_at
 * @property mixed $product_value
 * @property mixed $id
 * @property mixed $quality_rate
 * @property mixed $shipping_rate
 * @property mixed $communication_rate
 * @property mixed $comment
 * @property mixed $product_name
 */
class Feedback extends Model
{
    use Uuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    /**
     * Kinds of rates
     *
     * @var array
     */
    public static array $rates = ['quality_rate','communication_rate', 'shipping_rate'];

    /**
     * Feedback is posted on this product
     *
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this -> hasOne(Product::class,'id', 'product_id');
    }

    /**
     * Returns if this feedback has a product
     *
     * @return bool
     */
    public function hasProduct(): bool
    {
        return $this -> product_id != null;
    }

    public function buyer(): HasOne
    {
        return $this->hasOne(User::class,'id','buyer_id');
    }


    /**
     * Vendor of the feedback
     *
     * @return HasOne
     */
    public function vendor(): HasOne
    {
        return $this -> hasOne(Vendor::class, 'id', 'vendor_id');
    }

    /**
     * Set the Vendor of the feedback
     *
     * @param Vendor $vendor
     */
    public function setVendor(Vendor $vendor): void
    {
        $this -> vendor_id = $vendor -> id;
    }

    /**
     * Sets the buyer of the purchase associated with the feedback
     *
     * @param User $buyer
     */
    public function setBuyer(User $buyer): void
    {
        $this -> buyer_id = $buyer->id;
    }

    /**
     * Returns feedback type with first letter uppercase
     *
     * @return string
     */
    public function getType(): string
    {
        return ucfirst($this->type);
    }


    /**
     * Set the product of the feedback
     *
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this -> product_id = $product -> id;
    }

    /**
     * Returns buyer name in format b***r (for buyer)
     *
     * @return string
     */
    public function getHiddenBuyerName(): string{
        $buyer = $this->buyer->username;
        $firstChar = substr($buyer,0,1);
        $lastChar = substr($buyer,-1,1);
        return $firstChar.'***'.$lastChar;
    }

    /**
     * Returns 'during last month' | 'during last three months' | 'during last six months' | 'during past year' | 'more than a year ago'
     *
     * @return string
     */
    public function getLeftTime(): string{
        $now = Carbon::now();
        $time = Carbon::parse($this->created_at);
        $timePassed = $now->diffInMonths($time);
        if ($timePassed < 1){
            return 'During last month';
        } else if ($timePassed >=1 && $timePassed < 3){
            return 'During last three months';
        } else if ($timePassed >=3 && $timePassed < 6){
            return 'During last six months';
        } else if ($timePassed >=6 && $timePassed < 12){
            return 'During last year';
        } else if ($timePassed >=12 ){
            return 'More than a year ago';
        }
    }

    /**
     * Checks if feedback is low value
     *
     * @return bool
     */
    public function isLowValue(): bool {
        return $this->product_value < intval(config('marketplace.vendor_low_value_feedback'));
    }
}
