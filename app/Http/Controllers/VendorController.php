<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * @property vendor
 */
class VendorController extends Controller
{
    public function vendor(): View|Application|Factory
    {
           return view('profile.vendor',
           [
//               'myProducts'=> auth()->user()->pr
//               'vendor'=> auth()->user()->vendor
           ]);
        }
}
