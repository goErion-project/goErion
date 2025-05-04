<?php

namespace App\Http\Controllers;

use App\Marketplace\Bitmessage\Bitmessage;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;

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
    protected $address;

    public function __construct() {

        $this->middleware('auth');


        $this->enabled = config('bitmessage.enabled');
        $this->address = config('bitmessage.marketplace_address');
        if ($this->enabled) {
            $this->bitmessage = new Bitmessage(
                config('bitmessage.connection.username'),
                config('bitmessage.connection.password'),
                config('bitmessage.connection.host'),
                config('bitmessage.connection.port')
            );
        }
    }

    public function show() {

        return view('profile.bitmessage')->with([
            'enabled' => $this->enabled,
            'address' => $this->address,
            'user' => auth()->user()
        ]);
    }
    public function sendConfirmation(SendConfirmationRequest $request){
        if (!$this->enabled){
            session()->flash('errormessage','Bitmessage service disabled');
            return redirect()->back();
        }
        try{
            $request->persist($this->bitmessage,$this->address);
        } catch (RequestException $e){
            $e->flashError();
            return redirect()->back();
        }
        return redirect()->back();
    }

    public function confirmAddress(ConfirmAddressRequest $request){
        try{
            $request->persist();
        } catch (RequestException $e){
            $e->flashError();
            return redirect()->back();
        }
        return redirect()->back();
    }
}
