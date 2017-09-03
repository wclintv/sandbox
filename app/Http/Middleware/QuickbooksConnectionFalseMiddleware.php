<?php

namespace App\Http\Middleware;

use Closure;

class QuickbooksConnectionFalseMiddleware
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
        //check the session if the quickbooks connection has been validated.
        $conn = $request->session()->get('quickbooks_connection');

        if($conn == false)
        {
            return $next($request);
        }
        return redirect('/quickbooks/connect');        
    }
}
