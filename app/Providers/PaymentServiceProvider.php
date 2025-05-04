<?php

namespace App\Providers;

use App\Marketplace\Payment\BitcoinPayment;
use App\Marketplace\Payment\Coin;
use App\Marketplace\Payment\Escrow;
use App\Marketplace\Payment\Payment;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Return payment class
        $this -> app -> singleton(Payment::class, function($app, $parameters){
            return new Escrow($parameters['purchase']);
        });
        // Return coin class
        $this -> app -> singleton(Coin::class, function($app){
            return new BitcoinPayment();
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
