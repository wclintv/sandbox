<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sso\Intuit\IntuitSso;
use LightOpenID;
use Redirect;
use Auth;
use App\Models\User;
use App\Plugins\QuickbooksOnline\QuickbooksOnline;
use App\Models\Invitation;
use App\Models\QboMembership;

class SsoController extends Controller
{
	public function route_to_provider(Request $request, $provider, $method)
    {
    	switch($provider)
    	{
    		case 'intuit':
    			switch($method)
    			{
    				case 'login':
						return $this->intuit_login($request);
    				break;
    				case 'subscribeFromAppstore':
    					return $this->subscribeFromAppstore($request);
    				break;
    				case 'qbo_disconnect':
    					return $this->qbo_disconnect($request);
    				break;
    				case 'invitation':
    					return $this->invitation($request);
    				break;
    			}  			
    		break;
    	}
    }
    private function qbo_disconnect(Request $request)
    {    	
    	//this is a webhook for a disconnect from the appstore...intuit handles the oauth disconnect, 
    	//all we need to do is login the user, reset the office, and redirect to the connect page.
		$user = IntuitSso::login($request);
		if(is_object($user))
		{
			Auth::login($user, true);
			$user->qbo_membership->office->reset_oauth();
			$request->session()->put('quickbooks_connection', false);      
			return redirect('/quickbooks/connect');  
		}   
    }
    private function invitation(Request $request)
	{
		$user = IntuitSso::login($request);
		if(is_object($user))
		{
			//run validate test on the invitation before logging in the user.
			$result = false;
			//if the invitation exists...associate the user with a qbo company.
		    $invitation = Invitation::by_token($request['i_token']);
		    if($invitation != null)                        
		    {
		    	//If the invitation is not expired
		        if(!$invitation->expired())
		        {
		            //update the user profile
		            $user->firstname = $invitation->firstname;
		            $user->lastname = $invitation->lastname;
		            $user->save();

		            //add the user to the office membership
		            $membership = QboMembership::by_user_id_by_office_id($user->user_id, $invitation->office_id);
		            if($membership == null)
		            {
		                $membership = new QboMembership;
		            }
		            $membership->user_id = $user->user_id;
		            $membership->office_id = $invitation->office_id;
		            $membership->save(); 
		            $result = true;               
		        }
		        Invitation::destroy($invitation->invitation_id);  
		    }

		    //process the test result
		    if($result == true){
		    	Auth::login($user, true);	
		    	return redirect('/customers');    		
		    }
		    else
		    {
		    	return response('You invitation is expired or has been canceled, please ask your admin user to resend the invitation.', 404);
		    }						  				
		}   
    }
    private function subscribeFromAppstore(Request $request)
    {
		$user = IntuitSso::login($request);
		if(is_object($user))
		{
			Auth::login($user, true);		
			$request->session()->put('subscribeFromAppstore', true);
        	return QuickbooksController::oauth_start();					
		}	
	 	return response('problem subsribing from the appstore', 500);
    }
    private function intuit_login(Request $request)
    {
		$user = IntuitSso::login($request);
		if(is_object($user))
		{
			Auth::login($user, true);					
			return redirect('/customers');      				
		}			
    }
}
/*
	private static function openid_callback_url()
	{
		return url('/sso/intuit/login');
	}
		private static function openid_identity()
	{
		return 'https://openid.intuit.com/Identity-snapdsk';
	}
	private static function openid_realm()
	{
		return url('/');
	}
    public static function login(Request $request)
	{
		$params = $request->toArray();

		$openid = new LightOpenID(Self::openid_callback_url());

        //if the mode is NOT set...construct a request and redirect to the authURL
		if(!$openid->mode)
		{        	
			$openid->identity = Self::openid_identity();
			$openid->realm = Self::openid_realm();
			$openid->required = array(
				    'namePerson/friendly', 
                    'contact/email' , 
                    'contact/country/home',
                    'namePerson', 
                    'namePerson/first', 
                    'namePerson/last',
                    'pref/language', 
            );
			$openid->optional = array('namePerson/last','namePerson/first', 'namePerson/friendly');
			redirect()->to($openid->authUrl())->send();
		}
		elseif($openid->mode == 'cancel')
		{
			return 'User has canceled OpenId authentication!';
		}
		else
		{
			if ($openid->validate())
			{
                //check if the user is already in the snapdsk database.
                $user = User::by_openid($params['openid_claimed_id']);

                //if the user doesn't exist, create a new user
                if($user == null)
                {
                    $user = new User;
                }
                $user->email = $params['openid_sreg_email'];
                $user->name = $params['openid_sreg_fullname'];           
                $user->password = str_random(12);
                $user->openid_claimed_id = $params['openid_claimed_id'];
                $user->save();
                return $user;
			}
		}
        return false;
	}

*/