<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Office;
use App\Utility\QLog;
use DateTime;
use View;
use DB;
use Log;
use Auth;
use Mail;
use LightOpenID;
use App\Mail\Invite;
use App\Models\Customer;
use App\Models\CustomerSearchData;
use App\Models\User;
use App\Jobs\QbOnlineSync;
use Yajra\Datatables\Datatables;
use App\Plugins\QuickbooksOnline\QuickbooksOnline;
use App\Sso\Intuit\IntuitSso;
use Route;
use App\Plugins\QuickbooksOnline\QboConfig;


class TestController extends Controller
{
	//Generic Tests
	public function test1(Request $request)
	{
		dd(Route::currentRouteAction());
		$bool = QuickbooksOnline::test_connection(Auth::user()->qbo_membership->office);
		dd($bool);
		return response((string)$bool,200);
		$str = IntuitSso::login();
		dd($str);
	}
	public function test2(Request $request)
	{
		dd(QuickbooksOnline::requests(10));			
	}
	public function test3(Request $request)
	{
		dd(config('app.domain'));
		return ;
	}	
	public function test4(Request $request)
	{
	}	

	public function test_qbo(Request $request)
	{
		$user = Auth::user();

		if($user->qbo_membership != null)
		{
			$response = QuickbooksOnline::test_connection(Auth::user()->qbo_membership->office);
			if($response != null)
			{
				return json_encode($response);
			}			
		}

		return response('null', 200);
	}

	//Specialized Tests
	private function convert_datetime_to_qb_timestamp(DateTime $dt)
	{
		//'u' is the variable that holds microseconds.  
		//NOTE:  Quickbooks might accept microseconds, making this step unnecessary.
		$micro = $dt->format('u');
		//divide it by 1000 to get milliseconds
		$milli = $micro/1000;
		//'P' is the UTC offset...capture it.
		$offset = $dt->format('P');
		//Construct the final format.
		return $dt->format("Y-m-d\TH:i:s.") . $milli . $offset;
	}	
	public function eloquent_relationships_test(Request $request)
	{
		$office = Auth::user()->qbo_membership->office;

		echo(Auth::user()->qbo_membership);
		echo('<hr>');
		echo($office);
		echo('<hr>');
		echo($office->memberships);		
		echo('<hr>');
		echo(json_encode($office->users()));
		echo('<hr>');
		echo($office->invitations);


		//echo($office->users);
		return;
	}
	public function login_test(Request $request)
	{
		$openid = new LightOpenID('https://www.snapdsk.com/test_login');

        //if the mode is NOT set...construct a request and redirect to the authURL
		if(!$openid->mode)
		{        	
			$openid->identity = 'https://openid.intuit.com/Identity-643cfb72-bddb-4689-a402-569a71a71c56';
			$openid->realm = 'https://www.snapdsk.com';
			$openid->required = array('contact/email', 'namePerson', 'namePerson/first', 'namePerson/last', 'intuit/realmId');
			$openid->optional = array('contact/email', 'namePerson', 'namePerson/last','namePerson/first', 'intuit/realmId');
			return redirect()->to($openid->authUrl())->send();
		}
		elseif($openid->mode == 'cancel')
		{
			return 'User has canceled OpenId authentication!';
		}
		else
		{
			dd($openid);
			if ($openid->validate())
			{
				




				// $credentials = [
				// 'name' => $params['openid_sreg_fullname'],
				// 'password' => $params['openid_claimed_id'],
				// 'email' => $params['openid_sreg_email'],
				// 'openid_claimed_id' => $params['openid_claimed_id'],
				// 'openid_sig' => $params['openid_sig'],
				// 'openid_assoc_handle' => $params['openid_assoc_handle'],
				// ];
				// return $credentials;
			}
		}           
	}
	public function mail_test(Request $request)
	{
		$now = date('d/M/Y H:i:s');
		Mail::to('cory.vernon@live.com')->send(new Invite('Wayne', $now));
    }	
	public function pagination_test(Request $request)
	{
		$customers = Customer::paginate(10);
		return view('tests.test', compact('customers'));
	}
    public function time_stamps_test()
	{
		//capture the office by realmid.
		$office = Office::where('qb_realmid','123145709545989')->first();

		//force an update timestamp onto the office object. so we can compare current time.		
		$office->officename = "Reno";
		$office->save();

		//capture the sync_timestamp
		$sync_timestamp = $office->sync_timestamp;
		//convert timestamp into datetime so we can maniuplate it.
		$sync_timestamp_datetime = new DateTime($sync_timestamp);
		//Format sync_timestamp in quickbooks format.
		$sync_timestamp_formatted = $this->convert_datetime_to_qb_timestamp($sync_timestamp_datetime);
		//Format updated_at in timezone format
		$sync_timestamp_timezone = $sync_timestamp_datetime->format('Y-m-d H:i:s P T');


		//Capture 'updated_at' from office table.
		$updated_at = $office->updated_at;	
		//convert timestamp into datetime so we can maniuplate it.
		$updated_at_datetime = new DateTime($updated_at);
		//Format updated_at in quickbooks format.
		$updated_at_formatted = $this->convert_datetime_to_qb_timestamp($updated_at_datetime);
		//Format updated_at in timezone format
		$updated_at_timezone = $updated_at_datetime->format('Y-m-d H:i:s P T');

		//Capture UTC current time.
		$utc = new DateTime(date(''));
		//Format UTC in quickbooks format.
		$utc_formatted = $this->convert_datetime_to_qb_timestamp($utc);
		//Format updated_at in timezone format
		$utc_timezone = $utc->format('Y-m-d H:i:s P T');

		//Capture a raw quickbooks example
		$qb_timestamp_formatted = '2017-03-26T09:03:20.341-07:00';
		$qb_timestamp_datetime = new DateTime($qb_timestamp_formatted);
		$qb_timestamp = $qb_timestamp_datetime->format('Y-m-d H:i:s');
		$qb_timestamp_timezone = $qb_timestamp_datetime->format('Y-m-d H:i:s P T');



		echo('	
				<style>
					.output_table td{
						padding:5px;
					}
				</style>
				<br><br><br>
				<table class="output_table">
					<th>
						<td>MySql Format</td>
						<td>Quickbooks Format</td>						
						<td>Timezone Format</td>
					</th>
					<tr>
						<td>Quickbooks raw:</td>
						<td>' . $qb_timestamp . '</td>
						<td>' . $qb_timestamp_formatted . '</td>
						<td>' . $qb_timestamp_timezone . '</td>
					</tr>
					<tr>
						<td>Office sync_timestamp:</td>
						<td>' . $sync_timestamp . '</td>
						<td>' . $sync_timestamp_formatted . '</td>
						<td>' . $sync_timestamp_timezone . '</td>							
					</tr>
					<tr>
						<td>Office updated_at::</td>
						<td>' . $updated_at . '</td>
						<td>' . $updated_at_formatted . '</td>	
						<td>' . $updated_at_timezone . '</td>					
					</tr>				
					<tr>
						<td>Current UTC time:</td>						
						<td>' . $utc->format('Y-m-d H:i:s') . '</td>
						<td>' . $utc_formatted . '</td>
						<td>' . $utc_timezone . '</td>
					</tr>
				</table>
			');
	}	
	public function webhooks_test(Request $request)
	{
		$status = [];
		$data = json_decode('{"eventNotifications": [{"realmId": "123145773446734","dataChangeEvent": {"entities": [{"name": "Customer","id": "1","operation": "Update","lastUpdated": "2017-05-23T16:31:37.000Z"}]}}]}', true);

		//Log the webhook if the app is in 'debug' mode
        if(config('app.debug') == true)
        {
            QLog::webhooks(' ' . PHP_EOL . json_encode($data, JSON_PRETTY_PRINT));
        }     
		
		/*NOTE: If the Sync writes records back to QuickBooks then the WebHooks will be triggered again, this loop will continue until
		there are no newly updated/created records in BlueCard to be written back to QuickBooks on the Sync*/
		
		foreach($data['eventNotifications'] as $event)
		{
			//dd(QuickbooksController::sync($event['realmId']));
			//return QuickbooksController::sync($event['realmId']);
			$status = QuickbooksController::sync($event['realmId']);
			QLog::output(' ' . PHP_EOL . json_encode($status, JSON_PRETTY_PRINT));
		}
        
		return $status;
		//return QuickbooksController::sync($data['eventNotifications'][0]['realmId']);
	}	
}

