<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Marketplace\Bitmessage\Bitmessage;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitmessageController extends Controller
{
    /**
     * @var Repository|mixed
     */
    protected mixed $enabled;
    /**
     * Bitmessage client
     *
     * @var Bitmessage
     */
    protected Bitmessage $bitmessage;

    /**
     * Marketplace address
     *
     * @var Repository|mixed
     */
    protected mixed $address;

    public function __construct() {

        $this->middleware('admin_panel_access');


        $this->enabled = config('bitmessage.enabled');
        $this->address = config('bitmessage.marketplace_address');
        if ($this->enabled){
            $this->bitmessage = new Bitmessage(
                config('bitmessage.connection.username'),
                config('bitmessage.connection.password'),
                config('bitmessage.connection.host'),
                config('bitmessage.connection.port')
            );
        }
    }
    public function show(): View
    {
        //$receiver = 'BM-2cVPgkanYAinXZJ1a7bJM9VG7a4CmGhVV8';
        //dd($this->bitmessage->sendMessage($receiver,$this->address,'Hello World', 'Hello from marketplace'));
        //dd($this->bitmessage->broadcast($this->address,'Hello World', 'Hello from a marketplace'));
        $connectionTest = false;
        if ($this->enabled){
            $connectionTest = $this->bitmessage->testConnection();
        }


        return view('admin.bitmessage')->with([
            'enabled' => $this->enabled,
            'test' => $connectionTest,
            'address' => $this->address,
        ]);
    }
}
