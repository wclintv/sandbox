<?php
namespace App\Plugins\QuickbooksOnline;

use Illuminate\Support\Facades\Hash;
use App\Models\Office;
use App\Plugins\QuickbooksOnline\QboConfig;
use App\Utility\QLog;
use Redirect;
use OAuth;
use DB;

class QuickbooksOnline
{
	//Methods
	public static function clear_requests()
	{
		return DB::raw('delete from qbo_requests');
	}
	public static function disconnect(Office $office)
	{
		$result = false;
		$url = QboConfig::oauth_disconnect_base();
		//Start cUrl HTTP client
		$curl = curl_init();

		//Configure cUrl
		curl_setopt($curl, CURLOPT_HTTPHEADER, Self::get_headers('GET', $url, $office));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_HEADER, 1);

		//Send the http request and get a response
		$response = curl_exec($curl);

		//get any errors that exist.
		$err = curl_error($curl);

		//Capture last request information
		$http_info = curl_getinfo($curl);

		//Log the Request
		Self::log_request($http_info, $response, 'disconnect');

		//close http connection
		curl_close($curl);

		//If any cUrl errors exist...echo the error...else return the $response
		if ($err) 
		{
			return "cURL Error #:" . $err;
		} 
		else
		{	
			//parse response body (removing header)
			$response = substr($response,$http_info['header_size']); 

			//Handle Quickbooks return types
			if(Self::isJson($response))
			{
				$response = json_decode($response, true);
			}
			elseif(Self::isXml($response))
			{
				$response = simplexml_load_string($response);
			}

			if($response['ErrorCode'] == 0)
			{    
	            //reset oauth data.
	            $office->reset_oauth(null, null);
	            $result = true;
			}
		}
		return $result;
	}
	public static function get($query, Office $office)
	{
		//generate the endpoint by encoding the query
		$url = Self::url($query, $office->qb_realmid);

		//Start cUrl HTTP client
		$curl = curl_init();

		//Configure cUrl
		curl_setopt($curl, CURLOPT_HTTPHEADER, Self::get_headers('GET', $url, $office));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_HEADER, 1);

		//Send the http request and get a response
		$response = curl_exec($curl);

		//get any errors that exist.
		$err = curl_error($curl);

		//Capture last request information
		$http_info = curl_getinfo($curl);

		//Log the Request
		Self::log_request($http_info, $response, $query);

		//close http connection
		curl_close($curl);

		//If any cUrl errors exist...echo the error...else return the $response
		if ($err) 
		{
			return "cURL Error #:" . $err;
		}

		if($http_info['http_code'] == 200)
		{
			//parse response body (removing header)
			$response = substr($response,$http_info['header_size']); 

			//decode the response by return types
			if(Self::isJson($response))
			{
				$response = json_decode($response, true);
			}
			elseif(Self::isXml($response))
			{
				$response = simplexml_load_string($response);
			}	
		
			return $response;
		}
		return false;		
	}
	private static function get_headers($http_method, $url, Office $office)
	{
		//Create headers for http request
		$headers = array(
			"accept: application/json",
			"cache-control: no-cache",
			'authorization: ' . Self::get_oauth_header($http_method, $url, $office) . ',',		
			"content-type: application/json",	
			);
		return $headers;
	}
	private static function get_oauth_header($http_method, $url, Office $office)
	{
		$oauth_token = $office->oauth_token;	
		$oauth_token_secret = $office->oauth_token_secret;
		$oauth = new OAuth(QboConfig::oauth_consumer_key(), QboConfig::oauth_consumer_secret(), OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
		$oauth->setToken($oauth_token, $oauth_token_secret);
		$auth_header = $oauth->getRequestHeader($http_method,$url);
		return $auth_header;
	}
	public static function grant_url()
	{
		return QboConfig::oauth_callback_base();
	}	
	private static function isJson($string) 
	{
	 	json_decode($string);
	 	return (json_last_error() == JSON_ERROR_NONE);
	}
	private static function isXml($string)
	{
		libxml_use_internal_errors(true);
		$arr = simplexml_load_string($string);
		if(!$arr)
		{
			return false;
		}
		return true;
	}
	public static function last_request($type = null)
	{
		$r = DB::table('qbo_requests')->orderBy('request_id', 'desc')->first();
		return json_decode(json_encode($r),true);
	}	
	private static function log_request($info, $response, $query)
	{
		//log the request in the database;
		$r = [
			$info['request_header'],			//request_header
			substr($response,0,$info['header_size']), //response_header
			$query,			//request_query
			substr($response,$info['header_size'],10000), //response_body
			date('Y-m-d H:i:s'),
			$info['http_code']
		];
		DB::insert('insert into qbo_requests (request_header, response_header, request_query, response_body, sent, status_code) values (?,?,?,?,?,?)',$r);		
	}
	public static function post($url, $json, Office $office)
	{
		//generate the endpoint by encoding the query
		$url = Self::urlPost($url, $office->qb_realmid);
		
		//Start cUrl HTTP client
		$curl = curl_init();

		//Configure cUrl
		curl_setopt($curl, CURLOPT_HTTPHEADER, Self::get_headers('POST', $url, $office));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_POST, true);
		//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);	//this is the body json	
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		
		//Send the http request and get a response
		$response = curl_exec($curl);
		
		//get any errors that exist.
		$err = curl_error($curl);

		//Capture last request information
		$http_info = curl_getinfo($curl);

		//Log the Request
		Self::log_request($http_info, $response, $json);

		//close http connection
		curl_close($curl);

		//If any cUrl errors exist...echo the error...else return the $response
		if ($err) 
		{
			return "cURL Error #:" . $err;
		} 

		if($http_info['http_code'] == 200)
		{
			//parse response body (removing header)
			$response = substr($response,$http_info['header_size']); 

			//Handle Quickbooks return types
			if(Self::isJson($response))
			{
				return json_decode($response, true);
			}
			if(Self::isXml($response))
			{
				return simplexml_load_string($response);
			}				
		}

		return $response;
	}
	public static function reconnect(Office $office)
	{

		$url = QboConfig::oauth_reconnect_base();
		//Start cUrl HTTP client
		$curl = curl_init();

		//Configure cUrl
		curl_setopt($curl, CURLOPT_HTTPHEADER, Self::get_headers('GET', $url, $office));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_HEADER, 1);

		//Send the http request and get a response
		$response = curl_exec($curl);

		//get any errors that exist.
		$err = curl_error($curl);

		//Capture last request information
		$http_info = curl_getinfo($curl);

		//Log the Request
		Self::log_request($http_info, $response, 'reconnect');

		//close http connection
		curl_close($curl);

		//If any cUrl errors exist...echo the error...else return the $response
		if ($err) 
		{
			return "cURL Error #:" . $err;
		} 
		else
		{			
			//parse response body (removing header)
			$response = substr($response,$http_info['header_size']); 

			//Handle Quickbooks return types
			if(Self::isJson($response))
			{
				return json_decode($response, true);
			}
			if(Self::isXml($response))
			{
				return simplexml_load_string($response);
			}
			return $response;
		}
	}
	public static function redirect()
	{
		return redirect()->away(QboConfig::url_qb_application());	
	}
	public static function requests($limit = 100)
	{
		$request = DB::select('select * from qbo_requests order by request_id desc limit ?', [$limit]);
		return $request;
	}
	public static function test_connection(Office $office)
	{
		$result = false;
		//Test the connection by trying to retreive a customer count.
		$query = "SELECT COUNT(*) FROM Customer";
		Self::get($query, $office);

		$last_request = Self::last_request();

		//if the http response code is 299 or less...success!
		if($last_request['status_code'] <= 299)
		{
			$result = true;
		}
		return $result;
	}
	private static function url($query, $realmid)
	{
		if (!empty($realmid))
		{
			return QboConfig::url_qb_company_base() . $realmid . '/query?query=' . urlencode($query);
		}
		return false;
	}
	private static function urlPost($method, $realmid)
	{
		if (!empty($realmid))
		{
				return QboConfig::url_qb_company_base() . $realmid . '/' . $method;
		}
		return false;	
	}
	public static function webhook_verify($payload, $signature)
	{
		$hashed_payload = hash_hmac('sha256',$payload, QboConfig::webhooks_token());
		$base16_signature = bin2hex(base64_decode($signature));

		if($hashed_payload === $base16_signature)
		{
			return true;
		}
		return false;		
	}


	//UNUSED CODE
	/*

	public static $GetType;
	private static $GetStatus;
	private static $GetMethod;	
	private static $GetEndpoint;
	private static $GetRequestList = [];
	private static $GetResponseList = [];

	public static $PostType;
	private static $PostStatus; 
	private static $PostMethod;
	private static $PostEndpoint;
	private static $PostRequestList = [];
	private static $PostResponseList = [];

	private static function array_to_json(Array $list)
	{
		//hack a json string together from a php array of json strings(ie quickbooks json responses).

		$count = count($list);
		$i = 0;

		$result = '[';
		foreach($list as $json)
		{
			if(++$i === $count)
			{
				$result .= $json ;
			}
			else
			{
				$result .= $json . ',';
			}
			
		}
		$result .= ']';

		return $result;
	}

	private static function array_to_text(Array $list)
	{
		$result = null;

		if(empty($list))
		{
			$result = "No requests were sent.";
		}
		else
		{
			$i = 1;
			foreach($list as $item)
			{
				$result .= $i .')  ' . $item . PHP_EOL;
				$i += 1;
			}			
		}
		return $result;
	}
	private static function has_errors($response)
	{
		$result = false;
		if($response['QueryResponse'])
		{
			$result = false;
		}
		elseif($response['BatchItemResponse'])
		{
			if($response['BatchItemResponse'][0]['Fault'])
			{
				$result = true;
			}
		}
		return $result;
	}
	private static function log_get_request($info, $response)
	{
		//log the report if in debug mode
		if(config('app.debug') == true)	
		{
			$report = [
				'Request:' 		=> PHP_EOL. $info['request_header'],
				'Response:'     => substr($response,0,$info['header_size']),
				'Qbo Api Request List' 	=> Self::$GetRequestList,
				'Qbo Api Response List' => Self::$GetResponseList,			
			];
			QLog::qbo_request($report);
		}			
	}
	private static function log_post_request($info, $response)
	{
		//log the report if in debug mode
		if(config('app.debug') == true)	
		{
			$report = [
				'Request:' 		=> PHP_EOL. $info['request_header'],
				'Response:'     => PHP_EOL. substr($response,0,$info['header_size']),
				'Qbo Api Request List' 	=> Self::$PostRequestList,
				'Qbo Api Response List' => Self::$PostResponseList,			
			];
			QLog::qbo_request($report);
		}	
	}
	public static function reconnect_url()
	{
		return QboConfig::oauth_reconnect_base();
	}	
	public static function report()
	{
		$report = [
		'GetStatus' 			=> Self::$GetStatus,
		'GetMethod;' 			=> Self::$GetMethod,
		'GetEndpoint' 			=> Self::$GetEndpoint,
		'GetRequestCount'		=> count(Self::$GetRequestList),
		'GetRequestList'		=> Self::$GetRequestList,
		'GetRequestListStr'		=> Self::array_to_text(Self::$GetRequestList),
		'GetResponseCount'		=> count(Self::$GetResponseList),
		'GetResponseList'		=> Self::$GetResponseList,
		'GetResponseListStr'	=> json_encode(json_decode(Self::array_to_json(Self::$GetResponseList)), JSON_PRETTY_PRINT),
		
		'PostStatus;' 			=> 	Self::$PostStatus,
		'PostMethod' 			=> 	Self::$PostMethod,
		'PostEndpoint' 			=> 	Self::$PostEndpoint,
		'PostRequestCount'		=>	count(Self::$PostRequestList),
		'PostRequestList'		=> 	Self::$PostRequestList,
		'PostRequestListStr'	=>	Self::array_to_text(Self::$PostRequestList),
		'PostResponseCount'		=>	count(Self::$PostResponseList),
		'PostResponseList'		=>	Self::$PostResponseList,
		'PostResponseListStr'	=>  json_encode(json_decode(Self::array_to_json(Self::$PostResponseList)), JSON_PRETTY_PRINT),
		];
		return $report;
	}	


	*/
}
