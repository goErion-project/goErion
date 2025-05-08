<?php

namespace App\Models;

use App\Marketplace\Payment\FinalizeEarlyPayment;
use App\Traits\Experience;
use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @property array|mixed|null $profilebg
 * @property mixed $can_use_fe
 * @property mixed $user
 * @property mixed $experience
 * @property mixed $feedback
 * @property mixed $trusted
 */
class Vendor extends User
{
    use Uuids;
    use Experience;
    protected $table = 'vendors';
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vendor_level', 'about', 'created_at', 'updated_at'];

    /**
     * @return Collection of User instances of all admins
     */
    public static function allUsers(): Collection
    {
        $vendorIDs = Vendor::all() -> pluck('id');

        return User::query()->whereIn('id', $vendorIDs) -> get();
    }


    /**
     * Return a user instance of the vendor
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this -> hasOne(User::class, 'id', 'id');
    }

    /**
     * Returns collection of vendor sales
     *
     * @return HasMany
     */
    public function sales(): HasMany
    {
       return $this -> hasMany(Purchase::class, 'vendor_id', 'id') -> orderByDesc('created_at');
    }

    /**
     * Unread sales
     *
     * @return int
     */
    public function unreadSales(): int
    {
        return $this -> sales() -> where('read', false) -> count();
    }


    /**
     * Returns number of the sales which has a particular state or number of all sales
     *
     * @param string $state
     * @return int
     */
    public function salesCount(string $state = ''): int
    {
        // If state doesnt exist
        if(!array_key_exists($state, Purchase::$states))
            return $this -> sales() -> count();

        return $this -> sales() -> where('state', $state) -> count();
    }


    /**
     * Relationship one to many with feedback
     *
     * @return HasMany
     */
    public function feedback(): HasMany
    {
        return $this -> hasMany(Feedback::class, 'vendor_id', 'id');
    }

    /**
     * @return mixed
     */
    public function hasFeedback(): mixed
    {
        return $this -> feedback -> isNotEmpty();
    }

    /**
     * Count the number of feedback rates left on this vendor
     *
     * @return int
     */
    public function countFeedback(): int
    {
        return $this -> feedback() -> count();
    }

    /**
     * Returns all vendor's feedback by type
     *
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeedbackByType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return $this->feedback()->where('type',$type)->get();
    }

    /**
     * Count vendor's feedback by type
     *
     * @param string $type
     * @param int|null $months
     * @return int
     */
    public function countFeedbackByType(string $type, ?int $months = null): int
{
    $query = $this->feedback()->where('type', $type);
    if ($months !== null) {
        $now = Carbon::now();
        $start = $now->subMonths($months);
        $query->where('created_at', '>', $start);
    }
    return $query->count();
}

    /**
     * Return string number with two decimals of the average rate
     *
     * @param $type = [ 'quality_rate' | 'communication_rate' | 'shipping_rate' ]
     * @return string
     */
    public function avgRate($type): string
    {
        if(!$this -> hasFeedback())
            return '0.00';

        if(!in_array($type, Feedback::$rates))
            $type = 'quality_rate';

        if($this -> feedback -> isEmpty())
            return '0.00';

        return number_format($this -> feedback -> avg($type), 2);

    }

    /**
     * Checks if the vendor is trusted (set in an admin panel)
     *
     * @return bool
     */
    public function isTrusted(): bool{
        if ($this->trusted){
            return true;
        }
        $lvl = $this->getLevel();
        $positive = $this->countFeedbackByType('positive');
        $neutral = $this->countFeedbackByType('neutral');
        $negative = $this->countFeedbackByType('negative');
        $total = $positive+$negative+$neutral;
        if($total == 0 || $lvl == 1 || $positive == 0){
            return false;
        }
        $percentage = round(($positive / $total) * 100);
        if ($lvl >= config('marketplace.trusted_vendor.min_lvl') &&
            $total >= config('marketplace.trusted_vendor.min_feedbacks') &&
            $percentage >= config('marketplace.trusted_vendor.percentage_of_feedback_positive')
        ){
            return true;
        }

        return false;

    }

    /**
     * Checks if vendor should have DWC tag
     *
     * @return bool
     */
    public function isDwc(): bool{
        if ($this->countFeedbackByType('negative') > config('marketplace.vendor_dwc_tag_count')){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns rounded avg rate of the feedback, half rounded on down
     * 4.1 => 4
     * 4.67 => 4.5
     *
     * @param $type
     * @return float|int
     */
    public function roundAvgRate($type): float|int
    {
        $avgRateNumeric = (float)$this -> avgRate($type);
        return floor($avgRateNumeric * 2) / 2;
    }

    /**
     * If there is profile bg, return it, if not set random bg
     */
    public function getProfileBg(){
        if ($this->profilebg == null){
            $this->profilebg = Arr::random(config('vendor.profile_bgs'));
            $this->save();
        }

        return $this->profilebg;
    }

    /**
     * Vendors with most sales all time
     * @return mixed
     */
    public static function topVendors(): mixed
    {
        return Cache::remember('top_vendors_frontpage',config('marketplace.front_page_cache.top_vendors'),function(){
            return self::with('sales')
                ->join('purchases', 'purchases.vendor_id', '=', 'vendors.id')
                ->select('vendors.*', DB::raw('COUNT(purchases.id) as purchases_count')) // Avoid selecting everything from the stock table
                ->orderBy('purchases_count', 'DESC')
                ->groupBy('vendors.id')
                ->limit(5)
                ->get();
        });
    }
    /**
     * Vendors with most sales in the last 7 days
     * @return mixed
     */
    public static function risingVendors(): mixed
    {
        return Cache::remember('rising_vendors_frontpage',config('marketplace.front_page_cache.rising_vendors'),function(){
            return self::with('sales')
                ->join('purchases', 'purchases.vendor_id', '=', 'vendors.id')
                ->select('vendors.*', DB::raw('COUNT(purchases.id) as purchases_count')) // Avoid selecting everything from the stock table
                ->orderBy('purchases_count', 'DESC')
                ->groupBy('vendors.id')
                ->where('vendors.created_at','>=',Carbon::now()->subDays(7))
                ->limit(5)
                ->get();
        });

    }

    public function getId(){
        return $this->id;
    }

    /**
     * Can Vendor use FE?
     *
     * @return bool
     */
    public function canUseFe(): bool
    {
        return $this->can_use_fe == 1 && FinalizeEarlyPayment::isEnabled();
    }

    /**
     * Check if the vendor can use a specific product type
     *
     * @param string $type
     *
     * @return bool
     */
    public function canUseType(string $type): bool
    {
        if ($type == 'fe'){
            return $this->canUseFe();
        }
        return true;
    }

}
