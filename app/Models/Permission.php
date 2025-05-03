<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|mixed $user_id
 * @property mixed $name
 */
class Permission extends Model
{
    use Uuids;

    /**
     * Every permission belongs to one user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this -> belongsTo(User::class, 'user_id', 'id');
    }


    public function setUser(User $user): void
    {
        $this -> user_id = $user -> id;
    }
}
