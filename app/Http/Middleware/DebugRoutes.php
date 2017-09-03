<?php

namespace App\Http\Middleware;

use Closure;

class DebugRoutes
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

        if(config('app.debug') == true)
        {
            return $next($request);
        }
        return response('Unauthorized Request', 401);        
    }
}
