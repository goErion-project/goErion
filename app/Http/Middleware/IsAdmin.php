<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //if the user is not admin
        if (auth()->check())
        {
            if (auth()->user()->isAdmin())

                return $next($request);
        }
        return redirect()->route('home')->with('error', 'You do not have access to the admin panel.');
    }
}
