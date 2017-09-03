<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class QuickbooksAdminMiddleware
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
        if(Auth::user()->qb_is_admin == true)
        {
            return $next($request);
        }
        return response('Only an admin user can access this route.', 403);        
    }
}
