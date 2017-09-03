<?php

namespace App\Http\Middleware;

use Closure;
use App\Utility\QLog;

class SsoMiddleware
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
        //check the the request is coming from intuit
        QLog::sso($request->ip());

        //grant access to Single Signon Providers.

        return $next($request);
        

        
    }
}
