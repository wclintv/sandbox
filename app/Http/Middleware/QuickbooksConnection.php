<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Office;
use Auth;
use App\Plugins\QuickbooksOnline\QuickbooksOnline;

class QuickbooksConnection
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
        //if the session has the 'connected' variable, fetch it.
        if($request->session()->get('quickbooks_connection') != null)
        {
            //fetch the session 'connected' variable
            $connected = $request->session()->get('quickbooks_connection');

            //if quickbooks connection has already been established...bypass the connection test, and continue the request.
            if($connected == true)
            {                
                return $next($request);
            }
        }



        $result = false;

        $user = Auth::user();
        if($user->qbo_membership != null)
        {
            //fetch the office related to the current user.
            $office = $user->qbo_membership->office;   
            
            // //check if the office has an oauth token
            // if(is_null($office->oauth_token) || empty($office->oauth_token))
            // {
            //     //if there is no oauth token redirect to the user to connect to quickbooks page.
            //     redirect('quickbooks/connect');
            // }

            //if the office oauth token is about to expire...run reconnect procedure
            if($office->oauth_expired() == true)
            {
                QuickbooksOnline::reconnect($office);
            }    

            //Test the office connection with Quickbooks, if false, refresh the oauth token
            if(QuickbooksOnline::test_connection($office) == false)
            {
                //reset the users oauth data
                $office->reset_oauth(null, null);

                //set test result to false;
                $result = false;
            }
            else
            {
                $result = true;
            }

            //set the session tracker to the test result
            $request->session()->put('quickbooks_connection', $result);
            if($result == false)
            {
                return redirect('quickbooks/connect');
            }
            return $next($request);
        }
        return redirect('quickbooks/connect');
    }
}
