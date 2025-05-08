<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsVendor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //if logged user is not vendor
        if (auth()->check())
        {
            if (auth()->user()->isVendor())

                return $next($request);
        }
        return redirect()->route('home')->with('error', 'You do not have access to the vendor panel.');
    }
}
