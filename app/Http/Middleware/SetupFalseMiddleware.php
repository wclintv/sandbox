<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\Office;

class SetupFalseMiddleware
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
        if($user->qbo_membership != null)
        {
            $office = $user->qbo_membership->office;
            if($office != null)
            {
                if($office->qb_setup_complete == false)
                {
                    return $next($request);
                }      
                return response('you have already completed the setup procedure', 403);       
            }
        }
        return response('error in SetupFalseMiddleware', 500);         
    }
}
