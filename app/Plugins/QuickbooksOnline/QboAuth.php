<?php

namespace App\Plugins\QuickbooksOnline;

use Illuminate\Http\Request;
//use App\Plugins\QuickbooksOnline\QuickbooksOnline;
use App\Plugins\QuickbooksOnline\QboConfig;
use OAuth;


class QboAuth
{
	
	public static function get_access_token(Request $request)
	{
        $request_token;
        $access_token;

        if(!isset($request['oauth_token']))
        {
            $request_token = Self::oauth_request_token_get();
            $request->session()->put('oauth_token_secret', $request_token['oauth_token_secret']);
            return redirect(QboConfig::oauth_authorize_url() . '?oauth_token=' . $request_token['oauth_token']);       
        }

        if( isset($request['oauth_token']) && isset($request['oauth_verifier']))
        {            
            $request['oauth_request_secret'] = $request->session()->get('oauth_token_secret');
            $access_token = Self::oauth_access_token_get($request);            
            $access_token['realmid'] = $request['realmId'];
            $request->session()->forget('oauth_token_secret');
            return $access_token;
        }
	}


	public static function oauth_request_token_get()
	{
		$oauth = new OAuth(QboConfig::oauth_consumer_key(), QboConfig::oauth_consumer_secret(), OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
		//$oauth->enableDebug();
		//$oauth->disableSSLChecks();
		$request_token = $oauth->getRequestToken(QboConfig::oauth_request_url(), QboConfig::oauth_callback_base());
		return $request_token;
	}

	public static function oauth_access_token_get($params)
	{
		$oauth = new OAuth(QboConfig::oauth_consumer_key(), QboConfig::oauth_consumer_secret(), OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
		//$oauth->enableDebug();
		//$oauth->disableSSLChecks();
		$oauth->setToken($params['oauth_token'], $params['oauth_request_secret']);
		$access_token = $oauth->getAccessToken(QboConfig::oauth_access_url());
		return $access_token;		
	}

	public static function oauth_start()
    {
        view()->addLocation(app_path('Plugins/QuickbooksOnline/Auth'));
        return view('intuit_oauth')->with('grant_url', QboConfig::oauth_callback_base());
    }



}