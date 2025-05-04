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
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->get('otherParty')){
            session()->put('new_conversation_other_party',$request->get('otherParty'));
        }
        if(!session()->has('private_rsa_key_decrypted'))
            return redirect()->route('profile.messages.decrypt.show');
        return $next($request);
    }
}
