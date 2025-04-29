<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // The default theme is light if no cookie exists
        $theme = $request->cookie('theme', 'light');

        // Share a theme variable with all views
        view()->share('theme', $theme);

        return $next($request);


    }
}
