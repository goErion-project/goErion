<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanEditProducts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (
            auth()->user()->isVendor() ||
            auth()->user()->isAdmin() ||
            auth()->user()->hasPermission('products')
        ))
        return $next($request);
        return redirect()->route('home')->with('error', 'You do not have access to the admin panel.');
    }
}
