<?php

namespace App\Http\Middleware;

use Closure;

class AuthRoutesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->route() == "logout" || $request->route() == "login")
        {
            return $next($request);
        }
        return response('not found', 404);


        
    }
}
