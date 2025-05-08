<?php

namespace App\Models;

use App\Exceptions\RequestException;
use App\Marketplace\Payment\FinalizeEarlyPayment;
use App\Marketplace\Utility\CurrencyConverter;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Laravel\Scout\Searchable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $id
 * @property mixed $isDigital
 * @property mixed $price_from
 * @property mixed $digital
 * @property $quantity
 * @property mixed $description
 * @property mixed $rules
 * @property mixed $physical
 * @property false $active
 * @property string $coins
 * @property string $types
 * @property mixed|string $name
 * @property mixed $offers
 * @property mixed $images
 * @property mixed $category_id
 * @property int|mixed $user_id
 * @property mixed $mesure
 * @property mixed $feedback
 * @property mixed $user
 * @property int|mixed $featured
 * @property mixed $rule
 * @property Carbon|mixed|null $created_at
 * @property Category|mixed $category
 *
 * @method exists()
 * @method static findOrFail($id)
 * @method static count()
 */
class Product extends Model
{
    use Uuids;
   use Searchable;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    public static array $orderingMap = [
        'newer' => 'created_at',
        'name' => 'name',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];


    public static function frontPage(): LengthAwarePaginator
    {
        return self::where('active', 1)->paginate(config('marketplace.products_per_page'));
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }


    public function toSearchableArray(): array
    {
        // Ensure relationships are loaded only if they exist
        if ($this->relationLoaded('category') && $this->category) {
            $this->category->loadMissing('parents');
        }

        if (!$this->relationLoaded('user')) {
            $this->loadMissing('user');
        }

        if ($this->isPhysical() && !$this->relationLoaded('physical')) {
            $this->loadMissing('physical');
        }

        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'rule' => $this->rule,
            'quantity' => $this->quantity,
            'mesure' => $this->mesure,
            'created_at' => $this->created_at->toIso8601String(),
            'price' => $this->price_from,
            'type' => $this->isPhysical() ? 'physical' : ($this->isDigital() ? 'digital' : null),
        ];

        // Handle category with parents
        if ($this->relationLoaded('category') && $this->category) {
            $array['category'] = [$this->category->name];
            if ($this->category->relationLoaded('parents')) {
                foreach ($this->category->parents as $parent) {
                    $array['category'][] = $parent->name;
                }
            }
        }

        // Handle user
        if ($this->relationLoaded('user') && $this->user) {
            $array['user'] = $this->user->username;
        }

        // Handle physical product specific fields
        if ($this->isPhysical() && $this->relationLoaded('physical') && $this->physical) {
            $array['from_country_full'] = $this->physical->shipsFrom();
            $array['from_country_code'] = $this->physical->country_from;
        }

        return $array;
    }
    protected function getProductType(): string
    {
        if ($this->isPhysical()) {
            return 'physical';
        }
        if ($this->isDigital()) {
            return 'digital';
        }
        return 'unknown';
    }

    /**
     * Returns if the product is digital
     *
     * @return bool
     */
    public function isDigital(): bool
    {
        return DigitalProduct::where('id', $this->id)->exists();
    }

    public function digital(): HasOne
    {
        return $this->hasOne(DigitalProduct::class, 'id', 'id');
    }

    public function isAutodelivery(): bool
    {
        return $this -> digital && $this -> digital -> autodelivery;
    }

    public function isUnlimited(): bool
    {
        return $this -> digital && $this -> digital -> unlimited;
    }

    /**
     * Updates the quantity for autodelivery products
     */
    public function updateQuantity(): void
    {
        if ($this -> isAutodelivery()){
            $this -> quantity = $this -> digital -> newQuantity();
        }
    }

    /**
     * \App\Category of the product
     *
     * @return HasOne
     */
    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    /**
     * Returns if the product is physical
     *
     * @return bool
     */
    public function isPhysical(): bool
    {
        return PhysicalProduct::where('id', $this->id)->exists();
    }

    public function physical(): HasOne
    {
        return $this->hasOne(PhysicalProduct::class, 'id', 'id');
    }

    /**
     * Attribute that returns a type of the product
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return $this->isPhysical() ? 'physical' : 'digital';
    }

    /**
     * Accessor for description
     *
     * @return string
     */

    public function getDescriptionHtmlAttribute(): string
    {
        return nl2br($this -> description);
    }

    /**
     * Returns the short version of the description
     *
     * @return string
     */
    public function getShortDescriptionAttribute(): string
    {
        return substr($this -> description, 0, 200) . '...';
    }

    public function getRulesHtmlAttribute(): string
    {
        return nl2br($this -> rules);

    }


    /**
     * Returns the specific object of the product \App\PhysicalProduct or \App\DigitalProduct
     *
     * @return DigitalProduct|DigitalProduct[]|PhysicalProduct|PhysicalProduct[]|Collection|Model
     */
    public function specificProduct(): DigitalProduct|Model|Collection|array|PhysicalProduct
    {
        if ($this->isPhysical())
            return $this->physical;
        return $this->digital;
    }

    /**
     * Collection of offers connected with this product
     *
     * @return HasMany
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'product_id', 'id')
//            ->where('deleted', '=', 0) // if the offer is not deleted
            ->orderBy('price');

    }

    /**
     * Returns best \App\Offer with the lowest price for the given $ quantity
     *
     * @param $quantity
     * @return Offer
     * @throws RequestException
     */
    public function bestOffer($quantity): Offer {
        $firstOffer = $this->offers()
            ->where('deleted', '=', 0)
            ->where('min_quantity', '<=', $quantity)
            ->orderBy('price')
            ->first();

        if ($firstOffer == null)
            throw new RequestException('There is no offer for this quantity!');
        return $firstOffer;
    }

    /**
     * Collection of images
     *
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'product_id');
    }

    /**
     * Returns the default \App\Image If there are no images returns placeholder instance of the \App\Image
     *
     * @return Model|HasMany|null|object
     */
    public function frontImage(): Model|HasMany|Image|null
    {
        if($this->images()->doesntExist())
        {
            $placeholderImage = new Image;
            $placeholderImage -> image = '../img/product.png';

            return $placeholderImage;
        }
        return $this->images()->where('first', true)->first() ?? $this->images()->first();
    }


    /**
     * Returns minimum price from a collection of connected offers
     *
     * @return mixed
     */
    public function getPriceFromAttribute(): mixed
    {
        return $this->offers->min('price');
    }

    /**
     * Returns number of orders
     *
     * @return int
     */
    public function getOrdersAttribute(): int
    {
        $numberOfOrders = 0;
        foreach ($this->offers()->get() as $offer) {
            $numberOfOrders += $offer->purchases()->count();
        }

        return $numberOfOrders;
    }

    /**
     * Returns the user of the product
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Subtract quantity of the products
     *
     * @param $amount
     * @throws RequestException
     */
    public function substractQuantity($amount): void
    {

        if ($amount > $this->quantity)
            throw new RequestException('Not enough items, it appears that someone bought in the meantime.');
        $this->quantity -= $amount;
        // if the product quantity is 0, delete it from the search index
        if($this->quantity == 0){
            $this->unsearchable();
        }
    }

    /**
     * Relationship one to many with feedback
     *
     * @return HasMany
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'product_id', 'id');
    }

    /**
     * @return mixed
     */
    public function hasFeedback(): mixed
    {
        return $this->feedback->isNotEmpty();
    }

    /**
     * Return string number with two decimals of the average rate
     *
     * @param $type
     * @return string
     */
    public function avgRate($type): string
    {
        if (!$this->hasFeedback())
            return '';

        if (!in_array($type, Feedback::$rates))
            $type = 'quality_rate';

        return number_format($this->feedback->avg($type), 2);

    }

    /**
     * Return which view will be shown when you click next in product editing or adding
     *
     * @return string
     */
    public function afterOffers(): string
    {
        if($this -> isDigital())
            return 'digital';
        return 'delivery';
    }

    /**
     *  Mark this product as inactive so nobody can see or edit
     */
    public function deactivate(): void
    {
        $this -> active = false;
        $this -> save();
        $this -> unsearchable();
    }

    /**
     * Returns if this product supports coin
     *
     * @param string $coin
     * @return bool
     */
    public function supportsCoin(string $coin): bool
    {
        return in_array($coin, explode(",", $this -> coins));
    }

    /**
     * Returns if this product supports a type of purchase
     *
     * @param $type
     * @return bool
     */
    public function supportsType($type): bool
    {
        return in_array($type, explode(",", $this -> types));
    }

    /**
     * Returns array of types for this product
     *
     * @return array
     */
    public function getTypes() : array
    {
        $types = explode(',', $this->types);
        $types = array_filter($types);
        $feName = FinalizeEarlyPayment::$shortName;
        if (!FinalizeEarlyPayment::isEnabled() && in_array($feName,$types)){
            unset($types[array_search($feName,$types)]);
        }
        return array_values($types);
    }

    /**
     * Sets the coins
     *
     * @param array $coins
     */
    public function setCoins(array $coins): void
    {
        $this -> coins = implode(',', $coins);
    }


    /**
     * Set the types of the coin
     *
     * @param array $types
     */
    public function setTypes(array $types): void
    {
        $this->types = implode(',', $types);
    }

    /**
     * Returns supported coins in an array
     *
     * @return array
     */
    public function getCoins() : array
    {
        $coinsFromProduct = explode(",", $this -> coins);
        return array_filter($coinsFromProduct, function($coinName){
            return in_array($coinName, array_keys(config('coins.coin_list')));
        });
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLocalPriceFrom(){

        return CurrencyConverter::convertToLocal($this->price_from);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLocalSymbol(): string
    {
        return CurrencyConverter::getLocalSymbol();
    }

}
