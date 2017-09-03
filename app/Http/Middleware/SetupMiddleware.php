<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\Office;

class SetupMiddleware
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
            //check if an office exists for the realmid
            $office = $user->qbo_membership->office;

            //if the office is not null
            if($office != null)
            {
                //if office setup has not been completed, redirect to setup sync page.
                if($office->qb_setup_complete == false)
                {
                    return redirect('/users/setup');
                }
            }    
            return $next($request);         
        }
        return redirect('/quickbooks/connect');
    }
}
