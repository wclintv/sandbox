<?php
namespace App\Sso\Intuit;

use Illuminate\Http\Request;
use LightOpenID;
use Redirect;
use Auth;
use App\Utility\QLog;
use App\Models\User;

class IntuitSso
{
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

	private static function openid_callback_url()
	{
		return url('/sso/intuit/login?openid_start=true');
	}
	private static function openid_identity()
	{
		return 'https://openid.intuit.com/Identity-snapdsk';
	}
	private static function openid_realm()
	{
		return url('/');
	}
}