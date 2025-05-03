<?php

namespace App\Traits;

/**
 * @method notifications()
 */
trait Notifiable
{
    /**
     * Create new notifications for a specified user
     *
     * @param string $content
     * @param string|null $routeName
     * @param string|null $routePramas
     */
    public function notify(string $content,string $routeName = null,string $routePramas = null): void
    {
        $this->notifications()->create(['description' => $content,'route_name'=>$routeName,'route_params'=> $routePramas]);

        /**
         * Bitmessage
         */
        if (config('bitmessage.enabled')){
            if ($this->bitmessage_address !== NULL){
                // if its enabled sent message
                BitmessageNotify::dispatch('Notification from marketplace',$content,$this->bitmessage_address)->delay(now()->addSecond(1));;
            }
        }
    }

    /**
     * Return user's unread notifications
     *
     * @return mixed
     */
    public function unreadNotifications(): mixed
    {
        return $this->notifications()->where('read',0);
    }
}
