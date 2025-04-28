<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Uuids;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed $referredBy
 * @property mixed $username
 * @property mixed|string $mnemonic
 * @property mixed|string $referral_code
 * @property mixed|string $msg_public_key
 * @property mixed|string $msg_private_key
 * @property int|mixed|null $referral_by
 * @property mixed|null $referred_by
 * @method static where(string $string, string $username)
 */
class User extends Authenticatable
{
    use Uuids;
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
}
