<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Marketplace\Utility\Captcha;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showSignIn(): View
    {
        return view('auth.signin')->with([
            'captcha'=> Captcha::Build()
        ]);
    }
}
