<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class DevMiddleware
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
        $user = Auth::user();
        if($user->highlander == true)
        {
            return $next($request);
        }
        return response('Access Denied.', 403);
    }   
}
