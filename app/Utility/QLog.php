<?php

namespace App\Utility;
use File;


class QLog
{
	private static $initialized = false;
	
	//Methods
	private static function initialize()
	{
		//initialize logs filesystem if it doesn't already exist...this revents file_put_contents errors.
		if(!file_exists(app_path('Utility/Logs')))
		{
			mkdir(app_path('Utility/Logs'), 0755, true);
		}
		if(!file_exists(app_path('Utility/Logs/sso.log')))
		{
			fopen(app_path('Utility/Logs/sso.log'), "w");
		}

		if(!file_exists(app_path('Utility/Logs/webhooks.log')))
		{
			fopen(app_path('Utility/Logs/webhooks.log'), "w");
		}
		if(!file_exists(app_path('Utility/Logs/output.log')))
		{
			fopen(app_path('Utility/Logs/output.log'), "w");
		}
		if(!file_exists(app_path('Utility/Logs/sync.log')))
		{
			fopen(app_path('Utility/Logs/sync.log'), "w");
		}
		if(!file_exists(app_path('Utility/Logs/email.log')))
		{
			fopen(app_path('Utility/Logs/email.log'), "w");
		}		
	}
	
	public static function login($user_id)
	{	
		if(Self::$initialized == false)
		{
			Self::initialize();
		}
		File::append(app_path('Utility/Logs/login.log'), 'LOGIN - USER_ID: ' . print_r($user_id, true) . ' - '  . date('d-M-Y h:i:s A e') . PHP_EOL);
	}
	public static function logout($user_id)
	{
		if(Self::$initialized == false)
		{
			Self::initialize();
		}
		File::append(app_path('Utility/Logs/login.log'), 'LOGOUT - USER_ID: ' . print_r($user_id, true) . ' - '  . date('d-M-Y h:i:s A e') . PHP_EOL);
	}
	public static function email($text)
	{
		if(Self::$initialized == false)
		{
			Self::initialize();
		}
		File::append(app_path('Utility/Logs/mail.log'), '******MAIL: ' . date('d-M-Y h:i:s A e') . PHP_EOL . print_r($text, true) . PHP_EOL);
	}
	public static function output($text)
	{
		if(Self::$initialized == false)
		{
			Self::initialize();
		}
		File::append(app_path('Utility/Logs/output.log'), '******OUTPUT: ' . date('d-M-Y h:i:s A e') . PHP_EOL . print_r($text, true) . PHP_EOL);
	}
	// public static function qbo_request($text)
	// {
	// 	File::append(app_path('Utility/Logs/qbo_requests.log'), '******QBO Request: ' . date('d-M-Y h:i:s A e') . PHP_EOL . print_r($text, true) . PHP_EOL . PHP_EOL);
	// }	
	public static function sso($text)
	{
		if(Self::$initialized == false)
		{
			Self::initialize();
		}
		
		File::append(app_path('Utility/Logs/sso.log'), '******Intuit OpenID server: ' . date('d-M-Y h:i:s A e') . PHP_EOL . print_r($text, true) . PHP_EOL);	
	}
	public static function sync($text)
	{
		if(Self::$initialized == false)
		{
			Self::initialize();
		}
		File::append(app_path('Utility/Logs/sync.log'), '******SYNC: ' . date('d-M-Y h:i:s A e') . PHP_EOL . print_r($text, true) . PHP_EOL);
	}
	// public static function test($text)
	// {
	// 	File::put(app_path('Utility/Logs/test.log'), '******TEST: ' . date('d-M-Y h:i:s A e') . PHP_EOL . print_r($text, true) . PHP_EOL);	
	// }
	public static function webhook($text)
	{
		if(Self::$initialized == false)
		{
			Self::initialize();
		}
		File::append(app_path('Utility/Logs/webhooks.log'), '******WEBHOOK: ' . date('d-M-Y h:i:s A e') . PHP_EOL . print_r($text, true) . PHP_EOL . PHP_EOL);	
	}



}

