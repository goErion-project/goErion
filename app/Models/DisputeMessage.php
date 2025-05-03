<?php

namespace App\Models;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property mixed|string $message
 * @property mixed $dispute_id
 * @property int|mixed $author_id
 * @property mixed $created_at
 *
 *
 */
class DisputeMessage extends Model
{
    use Uuids;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * \App\Dispute instance
     *
     * @return BelongsTo
     */
    public function dispute(): BelongsTo
    {
        return $this -> belongsTo(Dispute::class, 'dispute_id');
    }

    /**
     * Set dispute of this message
     *
     * @param Dispute $dispute
     */
    public function setDispute(Dispute $dispute): void
    {
        $this -> dispute_id = $dispute -> id;
    }

    /**
     * Set the author of the message
     *
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this -> author_id = $author -> id;
    }

    /**
     * Return author of the purchase
     *
     * @return HasOne
     */
    public function author(): HasOne
    {
        return $this -> hasOne(User::class, 'id', 'author_id');
    }

    /**
     * Pastime since a message is created
     *
     * @return string
     */
    public function getTimeAgoAttribute(): string
    {
        return Carbon::parse($this -> created_at) -> diffForHumans();
    }
}
