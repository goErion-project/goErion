<?php

namespace App\Models;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property mixed $created_at
 */
class PGPKey extends Model
{
    use Uuids;

    protected $table = 'pgpkeys';

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function timeUntil(): Carbon
    {
        return new Carbon($this->created_at);
    }
}
