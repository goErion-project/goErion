<?php

namespace App\Http\Controllers;

use App\Exceptions\RequestException;
use App\Http\Requests\Bitmessage\ConfirmAddressRequest;
use App\Http\Requests\Bitmessage\SendConfirmationRequest;
use App\Marketplace\Bitmessage\Bitmessage;
use Illuminate\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendConfirmation(SendConfirmationRequest $request): RedirectResponse
    {
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function confirmAddress(ConfirmAddressRequest $request): RedirectResponse
    {
        try{
            $request->persist();
        } catch (RequestException $e){
            $e->flashError();
            return redirect()->back();
        }
        return redirect()->back();
    }
}
