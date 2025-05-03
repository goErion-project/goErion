<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanReadMessages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->otherParty){
            session()->put('new_conversation_other_party',$request->otherParty);
        }
        if(!session()->has('private_rsa_key_decrypted'))
            return redirect()->route('profile.messages.decrypt.show');
        return $next($request);
    }
}
