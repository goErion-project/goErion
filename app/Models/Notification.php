<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property true $read
 * @property mixed $route_params
 * @property mixed $route_name
 */
class Notification extends Model
{
    use Uuids;
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['description','route_name','route_params'];

    /**
     * Returns user that notifications are sent to
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user read notification
     *
     * @return bool
     */
    public function isRead(): bool {
        return $this->read == 1;
    }

    /**
     * Get route params
     *
     * @return mixed
     */
    public function getRouteParams(): mixed
    {
        return unserialize($this->route_params);
    }

    /**
     * Get a route name
     *
     * @return mixed
     */
    public function getRoute(): mixed
    {
        return $this->route_name;
    }

    /**
     * Mark the notification as read
     */
    public function markAsRead(): void
    {
        $this->read = true;
        $this->save();
    }
}
