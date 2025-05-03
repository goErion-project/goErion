<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //if the user is logged in and has not checked the validation string
        if (auth()->check() && auth()->user()->login_2fa && session()->has('login_validation_string'))
            return redirect()->route('auth.verify');
        return $next($request);
    }
}
