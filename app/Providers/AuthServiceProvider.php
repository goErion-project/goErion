<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Models\Product;
use App\Policies\ConversationPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * @method registerPolicies()
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected array $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Conversation::class => ConversationPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();


        // Gate for moderator
        Gate::define('has-access', function($user, $permission){
            return $user -> hasPermission($permission);
        });

        /**
         * Admin grants access to all
         */
        Gate::before(function($user, $permission){
            if($user -> isAdmin()){
                return true;
            }
        });
    }
}
