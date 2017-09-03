<?php

namespace App\Plugins\QuickbooksOnline;

//Quickbooks Always ON Parameters
define('OAUTH_ACCESS_URL'		,	'https://oauth.intuit.com/oauth/v1/get_access_token');
define('OAUTH_AUTHORISE_URL'	,	'https://appcenter.intuit.com/Connect/Begin');
define('OAUTH_DISCONNECT_BASE'	,	'https://appcenter.intuit.com/api/v1/connection/disconnect');
define('OPENID_IDENTITY'		,	'https://openid.intuit.com/Identity-snapdsk');
define('OAUTH_RECONNECT_BASE' 	,	'https://appcenter.intuit.com/api/v1/connection/reconnect');
define('OAUTH_REQUEST_URL'		,	'https://oauth.intuit.com/oauth/v1/get_request_token');

//Quickbooks Sandbox Environment
define('OAUTH_CONSUMER_KEY_SANDBOX'		,	'qyprd5sXYFOGzGxt3aUsRqQ1Noelgg');  //Sandbox
define('OAUTH_CONSUMER_SECRET_SANDBOX'	,	'tR3GAtI9f22Hl4JUkdRDDUF42fNlnBT4HiaCsEQx'); //Sandbox
define('URL_QBCOMPANY_BASE_SANDBOX'		,	'https://sandbox-quickbooks.api.intuit.com/v3/company/'); //Sandbox
define('URL_QBAPPLICATION_SANDBOX'		,   'https://sandbox.qbo.intuit.com'); //Sandbox
define('WEBHOOKS_TOKEN_SANDBOX'			,	'02da7bb5-d47b-4f87-a8bd-a35b56578f75');  //Sandbox

//Quickbooks Production Environment
define('OAUTH_CONSUMER_KEY'		,	'qyprdQTPT0v7kupmIrT28kIFIf9SIY'); //Production
define('OAUTH_CONSUMER_SECRET'	,	'l7ty4QapzqBqaXx6ixWqHsqo4ty5pAps7EDIWb4h'); //Production
define('URL_QBCOMPANY_BASE' 	,	'https://quickbooks.api.intuit.com/v3/company/'); //Production
define('URL_QBAPPLICATION'		,	'https://qbo.intuit.com');  //Production
define('WEBHOOKS_TOKEN'			,	'78132d8c-4a5d-4e34-b837-bf632a9d2dca');  //Production

class QboConfig
{
	/*
		QboConfig returns the settings needed by Quickbooks Online Api
		Quickbooks Api has two basic environments, Sandbox, and Production.
		QboConfig checks the .env file for the QB_SANDBOX variable and then
		returns the appropriate setting.
	*/

	public static function oauth_access_url()
	{
		return OAUTH_ACCESS_URL;
	}
	public static function oauth_authorize_url()
	{
		return OAUTH_AUTHORISE_URL;
	}
	public static function oauth_callback_base()
	{
		return url('/quickbooks/oauth_callback');
	}
	public static function oauth_consumer_key()
	{
		if(config('app.qb_sandbox') == true)
		{
			return OAUTH_CONSUMER_KEY_SANDBOX;
		}
		return OAUTH_CONSUMER_KEY;			
	}
	public static function oauth_consumer_secret()
	{
		if(config('app.qb_sandbox') == true)
		{
			return OAUTH_CONSUMER_SECRET_SANDBOX;
		}
		return OAUTH_CONSUMER_SECRET;
	}	
	public static function oauth_disconnect_base()
	{
		return OAUTH_DISCONNECT_BASE;
	}
	public static function oauth_reconnect_base()
	{
		return OAUTH_RECONNECT_BASE;
	}	
	public static function oauth_request_url()
	{
		return OAUTH_REQUEST_URL;
	}
	public static function openid_callback_url()
	{
		return url('/sso/login');
	}
	public static function openid_identity()
	{
		return OPENID_IDENTITY;
	}
	public static function openid_realm()
	{
		return url('/');
	}
	public static function url_qb_application()
	{
		if(config('app.qb_sandbox') == true)
		{
			return URL_QBAPPLICATION_SANDBOX;			
		}
		return URL_QBAPPLICATION;			
	}
	public static function url_qb_company_base()
	{
		if(config('app.qb_sandbox') == true)
		{
			return URL_QBCOMPANY_BASE_SANDBOX;
		}
		return URL_QBCOMPANY_BASE;
	}
	public static function webhooks_token()
	{
		if(config('app.qb_sandbox') == true)
		{
			return WEBHOOKS_TOKEN_SANDBOX;			
		}
		return WEBHOOKS_TOKEN;			
	}
}
?>