<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Adminable;
use App\Traits\Uuids;
use App\Traits\Vendorable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

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
 * @method static where(string $string, string $username)
 */
class User extends Authenticatable
{
    use Uuids;
    use Vendorable;
    use Adminable;
    /** @use HasFactory<UserFactory> */


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @throws \Exception
     */
    public static function findByUsername(string $username)
    {
        $user = self::where('username', $username)->first();
        if ($user === null)
        {
            throw new \Exception('User not found');
        }
        return $user;
    }

    public function hasPGP(): bool
    {
        return $this->pgp_key !== null;
    }

    public function pgpKeys(): HasMany
    {
        return $this->hasMany(PGPKey::class, 'user_id', 'id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function referredBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'referredd_by');
    }

    public function hasReferredBy(): bool
    {
        return $this->referredBy !== null;
    }

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


    public function memberSince(): string
    {
        return date_format($this->created_at, 'M/Y');
    }

    public function getId()
    {
        return $this->id;
    }
}
