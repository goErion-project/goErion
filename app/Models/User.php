<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Exceptions\RequestException;
use App\Marketplace\Utility\CurrencyConverter;
use App\Traits\Adminable;
use App\Traits\Displayable;
use App\Traits\Uuids;
use App\Traits\Vendorable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * @property mixed $referredBy
 * @property mixed $username
 * @property mixed|string $mnemonic
 * @property mixed|string $referral_code
 * @property mixed|string $msg_public_key
 * @property mixed|string $msg_private_key
 * @property int|mixed|null $referral_by
 * @property mixed|null $referred_by
 * @property mixed $pgp_key
 * @property Carbon|mixed|null $created_at
 * @property int|mixed $id
 * @property mixed $last_seen
 * @property mixed $admin
 * @property mixed $vendor
 * @property bool|mixed $login_2fa
 * @property mixed $vendorPurchases
 * @method static where(string $string, string $username)
 */
class User extends Authenticatable
{
    /**
     * Traits used by User model
     */
    use Notifiable;
    use Uuids;
    use Vendorable;
    use Adminable;
    use Displayable;

    /**
     * Permissions of the User
     *
     * @var array
     */
    public static array $permissions = ['categories', 'messages', 'users', 'products', 'logs', 'disputes', 'tickets', 'vendorpurchase', 'purchases'];
    public static array $permissionsLong = [
        'categories' =>'Categories',
        'messages' => 'Messages',
        'users' => 'Users',
        'products' => 'Products',
        'logs' => 'Logs',
        'disputes' => 'Disputes',
        'tickets' => 'Tickets',
        'vendorpurchase' => 'Vendor Purchases',
        'purchases' => 'Purchases'
    ];

    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Returns User with only a username which is not persisted in db used for market conversations
     *
     * @return User
     */
    public static function stub(): User
    {
        $stubUser = new User();
        $stubUser -> username = 'MARKET MESSAGE';
        return $stubUser;
    }

    /**
     * Collection of users just buyers
     *
     * @return User[]|Collection
     */
    public static function buyers(): Collection|array
    {
        $allUsers = User::all();
        $onlyBuyers = $allUsers -> diff(Admin::allUsers());
        return $onlyBuyers -> diff(Vendor::allUsers());
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return User
     */
    public static function findByUsername(string $username): User {
        $user = self::where('username',$username)->first();
        if ($user == null ){
            throw new NotFoundHttpException('User not found');
        }
        return $user;
    }

    /**
     * Overrides remember token setting during logout
     * @param string $value
     */
    public function setRememberToken($value)
    {
        // do nothing
    }

    /**
     * Determines if the user has pgp key set
     *
     * @return bool
     */
    public function hasPGP(): bool
    {
        return $this -> pgp_key != null;
    }

    /**
     * Collection of old keys that are not in usage
     *
     * @return HasMany
     */
    public function pgpKeys(): HasMany
    {
        return $this -> hasMany(PGPKey::class, 'user_id', 'id');
    }

    /**
     * Sets the login 2fa on or off
     *
     * @param $turn
     * @throws RequestException
     */
    public function set2fa($turn): void
    {
        if($turn && $this -> pgp_key == null)
            throw new RequestException("To turn on the Two Factor Authetication you will need to add PGP key first!");
        else{
            // set the login 2fa
            $this -> login_2fa = $turn == true;
            $this -> save();
        }
    }

    /**
     * Return user's notifications
     *
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this -> hasMany(Notification::class);
    }

    /**
     * Return Product that a user has
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this -> hasMany(Product::class, 'user_id') -> where('active', true) -> orderByDesc('created_at');
    }

    /**
     * Return a number of recent products
     *
     * @param int $amount
     * @return Collection|\Illuminate\Support\Collection
     */
    public function recentProducts(int $amount = 3): Collection|\Illuminate\Support\Collection
    {
        return $this -> products() -> take($amount) -> get();
    }

    /**
     * Returns collection of whishes
     *
     * @return HasMany
     */
    public function whishes(): HasMany
    {
        return $this -> hasMany(Wishlist::class, 'user_id', 'id');
    }

    /**
     * Returns true if this user is whishing product
     *
     * @param Product $product
     * @return bool
     */
    public function isWhishing(Product $product): bool
    {
        return Wishlist::added($product, $this);
    }

    public function getMsgPublicKeyAttribute($value) {
        return decrypt($value);
    }
    public function setMsgPublicKeyAttribute($value): void
    {
        $this->attributes['msg_public_key'] = encrypt($value);
    }
    public function getMsgPrivateKeyAttribute($value) {
        return decrypt($value);
    }
    public function setMsgPrivateKeyAttribute($value): void
    {
        $this->attributes['msg_private_key'] = encrypt($value);
    }

    /**
     * Returns string time how long passed since the user joined
     *
     * @return string
     */
    public function getJoinedAttribute(): string
    {
        return Carbon::parse($this -> created_at) -> diffForHumans();
    }

    /**
     * Define the relationship of purchases
     *
     * @return HasMany
     */
    public function purchases(): HasMany
    {
        return $this -> hasMany(Purchase::class, 'buyer_id', 'id');
    }

    /**
     * Define vendor relationship of purchases
     *
     * @return HasMany
     */
    public function sales(): HasMany
    {
        return $this -> hasMany(Purchase::class, 'vendor_id', 'id');
    }


    /**
     * Returns the number of all purchases for this user or the number of purchases in a particular state
     *
     * @param string $state
     * @return int
     */
    public function purchasesCount(string $state = ''): int
    {
        if(!array_key_exists($state, Purchase::$states))
            return $this -> purchases() -> count();

        return $this -> purchases() -> where('state', $state) -> count();
    }

    /**
     * Set the bitcoin address
     *
     * @param $address
     * @param string $coin
     */
    public function setAddress($address, string $coin = 'btc'): void
    {
        $newAddress = new Address;
        $newAddress -> address = $address;
        $newAddress -> user_id = $this -> id;
        $newAddress -> coin = $coin;
        $newAddress -> save();
    }

    /**
     * Relationship with the conversations where the user is sender
     *
     * @return HasMany
     */
    public function senderconversations(): HasMany
    {
        return $this -> hasMany(Conversation::class, 'sender_id', 'id');
    }

    /**
     * Relationship with the conversations where the user is sender
     *
     * @return HasMany
     */
    public function receiverconversations(): HasMany
    {
        return $this -> hasMany(Conversation::class, 'receiver_id', 'id');
    }

    /**
     * All conversations as Query Builder
     *
     * @return HasMany
     */
    public function conversations(): HasMany
    {
        return Conversation::query()->where('sender_id', $this -> id) -> orWhere('receiver_id', $this -> id);
    }

    /**
     * @return Collection
     */
    public function getConversationsAttribute(): \Illuminate\Support\Collection
    {
        return $this -> conversations() -> get();
    }

    /**
     * Return a collection of addresses
     *
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this -> hasMany(Address::class, 'user_id', 'id');
    }

    /**
     * Returns the most recent address of the given coin for this user
     *
     * @param $coin
     * @return Address
     * @throws \Exception
     */
    public function coinAddress($coin): Address
    {
        if(!in_array($coin, array_keys(config('coins.coin_list'))))
            throw new RequestException('Purchase completion attempt unsuccessful, coin not suported by marketpalce');

        $usersAddress = $this->addresses()->where('coin', $coin)->orderByDesc('created_at')->first();
        if(is_null($usersAddress) && $coin == 'btcm')
            throw new RequestException('User ' . $this -> username . ' doesn\'t have a valid public key for making multisig address!');
        if(is_null($usersAddress))
            throw new RequestException('User ' . $this -> username . ' doesn\'t have a valid address for sending funds! If this is user who referred you please notify him!');
        return $usersAddress;
    }

    /**
     * Returns how many addresses have user for this $coin
     *
     * @param $coin
     * @return int
     * @throws RequestException
     */
    public function numberOfAddresses($coin): int
    {
        if(!in_array($coin, array_keys(config('coins.coin_list'))))
            throw new RequestException('There is no coin under that name!');

        return $this -> addresses() -> where('coin', $coin) -> count();
    }


    /**
     * Returns registration date in format: Month/Year (Jan/2018)
     *
     * @return string
     */
    public function memberSince(): string
    {
        return date_format($this->created_at,"M/Y");
    }

    /**
     * Generate deposit addresses for this User
     */
    public function generateDepositAddresses(): void
    {
        $coinsClasses = config('coins.coin_list');

        // vendor fee in usd
        $marketVendorFee =  config('marketplace.vendor_fee');

        // for each supported coin generate an instance of the coin
        foreach ($coinsClasses as $short => $coinClass){
            $coinsService = new $coinClass();
            try {
                // Add a new deposit address
                $newDepositAddress = new VendorPurchase;
                $newDepositAddress->user_id = $this->id;

                $newDepositAddress->address = $coinsService->generateAddress(['user' => $this->id]);
                $newDepositAddress->coin = $coinsService->coinLabel();

                $newDepositAddress->save();
            }catch(\Exception $e){
                \Illuminate\Support\Facades\Log::error($e);
            }
        }
    }


    /**
     * One-to-many relationship with the deposit addresses
     *
     * @return HasMany
     */
    public function vendorPurchases(): HasMany
    {
        return $this -> hasMany(VendorPurchase::class, 'user_id', 'id');
    }

    /**
     * Relationship with the User who referred $this user
     *
     * @return HasOne
     */
    public function referredBy(): HasOne
    {
        return $this -> hasOne(User::class, 'id', 'referred_by');
    }

    /**
     * Returns if the user has referred by user
     *
     * @return bool
     */
    public function hasReferredBy(): bool
    {
        return $this -> referredBy != null;
    }

    /**
     * Relationship with permissions, User can have 0.* permissions
     *
     * @return HasMany
     */
    public function permissions(): HasMany
    {
        return $this -> hasMany(Permission::class, 'user_id', 'id');
    }

    /**
     * Returns true if the user has any permission
     *
     * @return bool
     */
    public function hasPermissions(): bool
    {
        return $this -> permissions() -> exists();
    }

    /**
     * Returns if the user has specific permission
     *
     * @param $name
     * @return bool
     */
    public function hasPermission($name): bool
    {
        return $this -> permissions() -> where('name', $name) -> exists();
    }

    /**
     * Deletes all old permissions and sets the new permissions
     *
     * @param array $permissions
     * @throws RequestException
     * @throws Throwable
     */
    public function setPermissions(array $permissions): void
    {
        // check if there are forbidden permissions
        if(!empty(array_diff($permissions, self::$permissions)))
            throw new RequestException("There are forbidden permissions!");

        try {
            DB::beginTransaction();
            // delete old permissions
            Permission::query()->where('user_id', $this->id)->delete();

            // insert new permissions
            foreach ($permissions as $inputPermission) {
                $newPermission = new Permission;
                $newPermission->name = $inputPermission;
                $newPermission->setUser($this);
                $newPermission->save();
            }

            DB::commit();
            event(new UserPermissionsUpdated($this, auth()->user()->admin));
        }
        catch (\Exception $e){
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error($e);
            throw new RequestException("Error happened with the database please try again!");
        }
    }

    /**
     * Relationship with the tickets
     *
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this -> hasMany(Ticket::class, 'user_id', 'id');
    }

    /**
     * Collection of tickets replies
     *
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this -> hasMany(TicketReply::class, 'user_id', 'id');
    }


    /**
     * Relationship with the bans
     *
     * @return HasMany
     */
    public function bans(): HasMany
    {
        return $this->hasMany(Ban::class, 'user_id', 'id');
    }

    /**
     * Returns bool if the user is banned
     *
     * @return bool
     */
    public function isBanned() : bool
    {
        if(!$this -> bans() ->exists()) return false;

        // Find the ban sorted by time
        $latestBan = $this->bans()->orderByDesc('until')->first();

        // if the until time it is greater than now
        if(Carbon::parse($latestBan->until)->gte(Carbon::now()))
            return true;

        return false;
    }

    /**
     * Make a ban from now
     *
     * @param $days
     */
    public function ban($days): void
    {
        $newBan = new Ban;

        if($this->bans()->exists())
        {
            $latestBan = $this->bans()->orderByDesc('until')->first();
            if(Carbon::parse($latestBan->until)->lt(Carbon::now()->addDays($days)))
                $newBan = $latestBan;
        }


        $newBan -> user_id = $this -> id;
        $newBan -> until = Carbon::now()->addDays($days);
        $newBan -> save();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLocalCurrency(){
        if (!CurrencyConverter::isEnabled()){
            return 'USD';
        }
        return CurrencyConverter::getLocalCurrency();
    }

    public function getId(){
        return $this->id;
    }
}
