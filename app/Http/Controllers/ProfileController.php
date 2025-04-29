<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
//        $this->middleware('verify_2fa');
    }

    public function index(): View
    {
        return view('profile.index');
    }

    public function pgp(): View
    {
        return view('profile.pgp');
    }

    public function become(): View
    {
        return view('profile.become');
    }
}
