<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $performed_on
 * @property mixed $performed_id
 * @method static create(array $array)
 */
class Log extends Model
{
    use Uuids;
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';


    protected $fillable = [
        'user_id','type','description','performed_on','performed_id'
    ];
    private static array $types = [
        'edit',
        'delete',
        'message',
        'dispute/resolve'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function performedOn(): ?array
    {
        if ($this->performed_on == null) {
            return null;
        }
        $class = $this->performed_on;
        if ($class == 'App\User'){
            $user = $class::find($this->performed_id);
            return [
                'text' => $user -> username,
                'link' => route('admin.users.view',$user->id)
            ];
        }
        return null;
    }

    public static function enter($details): void
    {
        $performedOn = $details['performed_on'];
        self::create([
            'user_id' => $details['user_id'],
            'type' => $details['type'],
            'description' => $details['description'],
            'performed_on' => get_class($performedOn),
            'performed_id' => $performedOn->id,
        ]);
    }
}
