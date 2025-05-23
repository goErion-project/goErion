<?php

namespace App\Traits;

use Carbon\Carbon;

/**
 * @method hasPermissions()
 */
trait Displayable
{
    /**
     * Return last seen time in X time ago format
     * @return string
     */
    public function lastSeenForHumans(): string
    {
        if ($this->last_seen == null){
            return 'Never signed in';
        }
        $time = Carbon::createFromTimeString($this -> last_seen);
        return $time -> diffForHumans();
    }


    public function getUserGroup(): array
    {
        if ($this->admin !== null){
            return [
                'name' => 'Administrator',
                'badge' => true,
                'color' => 'warning'
            ];
        }
        if ($this->vendor !== null){
            return [
                'name' => 'Vendor',
                'badge' => true,
                'color' => 'info'
            ];
        }
        if ($this->hasPermissions()){
            return [
                'name' => 'Moderator',
                'badge' => true,
                'color' => 'secondary'
            ];
        }
        return [
            'name' => 'User',
            'badge' => false,
            'color' => 'default'
        ];
    }
}
