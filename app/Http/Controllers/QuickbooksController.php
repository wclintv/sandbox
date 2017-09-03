<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plugins\QuickbooksOnline\QuickbooksOnline;
use App\Plugins\QuickbooksOnline\QboConfig;
use App\Plugins\QuickbooksOnline\QboAuth;
use App\Plugins\QuickbooksOnline\Models\QueryResponse;
use App\Plugins\QuickbooksOnline\Models\BatchItemRequest;
use App\Plugins\QuickbooksOnline\Models\BatchItemResponse;
use App\Plugins\QuickbooksOnline\Models\QBCustomer;
use App\Plugins\QuickbooksOnline\Models\QBPaymentMethod;
use App\Plugins\QuickbooksOnline\Helpers\DateTimeConverter;
use Carbon\Carbon;
use Auth;
use Log;
use DateTime;
use App\Models\User;
use App\Models\CustomerData;
use App\Models\CustomerDataList;
use App\Models\CustomerSearchData;
use App\Models\Invitation;
use App\Models\Office;
use App\Models\PaymentMethod;
use App\Models\State;
use App\Models\QboMembership;
use App\Utility\QLog;

//include_once(app_path() . '/Plugins/QuickbooksOnline/config.php');

class QuickbooksController extends Controller
{
    //Constructor
    public function __construct()
    {
        //map laravels 'view' class to Quickbooksonline plugin
        view()->addLocation(app_path('Plugins/QuickbooksOnline/Auth'));
    }

    //Methods
    public static function all()
    {
        return QBCustomer::all(Auth::user()->qbo_membership->office);
    }    
    public static function connect()
    {
        return view('connect');
    }
    public static function customer_load()
    {
        // Get the Qbo office/company
        $office = Auth::user()->qbo_membership->office;        
     
        //Get all customers from quickbooks
        $QBCustomerList = QBCustomer::all($office);

        //iterate through the qbCustomer list
        foreach($QBCustomerList as $qbCustomer)
        {
            //Convert each qbCustomer into a CustomerData object and create in database
            $c = $qbCustomer->to_customerdata();
            $cd = CustomerData::create($c->toArray());
            unset($qbCustomer);
        }
        
        unset($QBCustomerList);

        //Update the global office timestamp
        Office::sync_timestamp_update($office->office_id);
        
        return CustomerData::all();  
    }    
  
    public static function disconnect(Request $request)
    {
        $office = Auth::user()->qbo_membership->office;
        
        //deauthorize the users oauth token
        if(QuickbooksOnline::disconnect($office))
        {
             //reset the session connection tracker.
            $request->session()->put('quickbooks_connection', false);       
            return back();
        }
        return false;        
    }
    public function media(Request $request, $filename)
    {
        $path = app_path('Plugins/QuickbooksOnline/Resources/img/' . $filename);
        return response()->file($path);
    }
    public function oauth_callback(Request $request)
    {
        $user = Auth::user();
        $access_token = QboAuth::get_access_token($request);

        if(is_array($access_token))
        {
            if($access_token['oauth_token'])
            {
                //add the current user to the access_token
                $access_token['admin_id'] = $user->user_id;

                //check if an office already exists by realmid...if null, create a new office....else, update the existing office
                //since qb_realmid is an encrypted value the 'where' clause in eloquent doesn't work.  the workaround is to query all,
                //then search for the realmid...this is done in the function 'by_realmid($reamlid)'.
                $office = Office::by_realmid($access_token['realmid']);
                if($office == null)
                {
                    $office = new Office;
                }
                $office->oauth_update($access_token);

                //test the connection by fetching the office name from Qbo.
                $office->fetch_name();

                //create a new membership to associate the user and qbo office.
                $membership = QboMembership::by_user_by_office($user, $office);
                if($membership == null)
                {                    
                    QboMembership::construct($user, $office);
                }

                //Update the user to qb_admin status.
                //Because this user received an OAuth token, we know they are the quickbooks admin user.
                $user->qb_is_admin = true;
                $user->save();

                //Handle Redirection
                if($request->session()->get('subscribeFromAppstore'))
                {
                    return redirect('/customers');
                }
                else
                {
                    // write JS refresh parent and close popup
                    echo '<script type="text/javascript">
                            window.opener.location.href = "/customers";
                            window.close();
                          </script>';
                    return;
                }
            }
        }
        return $access_token;
    }
    public static function oauth_start()
    {
        return QboAuth::oauth_start();
    }
    public static function redirect()
    {
        return QuickbooksOnline::redirect();
    }
    public static function sync(Office $office)
    {
        $msg = array();
		$msg['query'] = 0;
		$msg['requestCount'] = 0;
		$msg['responseCount']['valid'] = 0;
        $msg['responseCount']['fault'] = 0;
		
		QLog::output('WebHook realmID:' . $office->qb_realmid);
        
        /*NOTE: Anytime a customer record is updated in BlueCard the Office timestamp of the respective office is updated automatically 
         BECAUSE their are TIMESTAMP TRIGGERS built into each of the Primary Objects! as well as the Employee object*/
        
		//this instantiates a NEW office object and illustrates another way to use Eloquent static calls
		//$office = Office::where('qb_realmid', Auth::user()->qb_realmid)->get(); //might be "first" instead of "get"
		
		// if(empty($realmID))
		// {
		// 	$realmID = Auth::user()->qb_realmid;
		// }
		
        $office_id = $office->office_id; //get the office id by realmid
		$officeIdList = '{"officeidlist": ["' . $office_id . '","8"]}';//CustomerDataList objects recieve lists of offices
		
		$sync_timestamp = Office::sync_timestamp_get($office_id);//timestamp returned in the BlueCard format 
		$sync_timestampQB = Office::sync_timestampQB_get($office_id); //timestamp returned in the QuickBooks format 
		
		QLog::output('WebHook $sync_timestampQB->stamp:' . $sync_timestampQB->stamp);
		
        ///////////////////////////////////////////////////////////*QuickBooks_QueryResponse ...i.e. SyncCustDataFromQB *//////////////////////////////////////////////
       
		$queryResp = new QueryResponse;
		//this function retrieves the cutomer records from QBs that have been updated since the last sync; it returns false if there are no new updates
		if($queryResp->CustomerList_GetUpdates($sync_timestampQB->stamp, $office))
		{
			//QLog::output('WebHook $queryResp:' . $queryResp);
			
			//The SyncFromQB function checks for potential conflicts with BC records then updates or creates records 
			//in BC from the QBs query response; it also updates/creates the CustomerSearchData	
			$msg['query'] = $queryResp->SyncFromQB($office_id, $officeIdList, $sync_timestamp->stamp);
		}
		
        //////////////////////////////////////////////////////////*QuickBooks_BatchItemRequest ...i.e. SyncCustDataToQB */////////////////////////////////////////////
        /*QuickBooks will only allow a maximum of 30 CustomerData records sent per batch request so we will have to divide them up through Pagination*/
        $startposition = 0;//MySql databases start with 0 not 1
        $requestObj = array();
        
        /*Create a CustomerDataList and load it to find out how many BC records have been updated since last Sync*/
        $custDataList = new CustomerDataList;
        $custDataList->DeserializeJson($officeIdList);
        $custDataList->GetUpdates($sync_timestamp->stamp);//this function retrieves the cutomer records from BC that have been updated since the last sync
        $sets = ceil($custDataList->recordCount/30);//divide the CustomerDataList count of records needing to be updated in QBs into sets of 30 rounded up
        
        unset($custDataList);//clears out the variable  
        
        if(!empty($sets))
        {
            /*This for loop creates the BatchItemRequest's in sets of 30*/
            for($x = 0; $x < $sets; $x++) 
            {
                /*Create a CustomerDataList and load it with each set of customers we want to send to QuickBooks*/
                $custDataList = new CustomerDataList;
                $custDataList->DeserializeJson($officeIdList);
                //Populate QueryResponse array
                $custDataList->GetUpdates_Pagination($startposition, $sync_timestamp->stamp);//this function retrieves the cutomer records from BC that have been Updated or Created since the last sync in groups of 30
                
                /*Remove customers from the CustomerDataList that were just updated by the QueryResponse*/
				if(!empty($queryResp->QueryResponse))
				{
					$num_rows = count($queryResp->QueryResponse->Customer);
					for($x = 0; $x < $num_rows; $x++) 
					{ 
						foreach($custDataList->CustomerDataList as $key=>$custData)
						{
							if($custData->Customer->qbid == $queryResp->QueryResponse->Customer[$x]->Id)
							{
								unset($custDataList->CustomerDataList[$key]);
							//If the same record was created in both BC and QBs the QueryResponse sync above will have already thrown a conflict
							//However, since the BC record was created AFTER the last Sync we have to make sure it is not picked up in the BatchItemRequest
							} elseif (($custData->Customer->firstname == $queryResp->QueryResponse->Customer[$x]->GivenName) &&
										($custData->Customer->lastname == $queryResp->QueryResponse->Customer[$x]->FamilyName))
							{
								unset($custDataList->CustomerDataList[$key]);
							}   
						}
					}  
				}			
                
				/*Map the BlueCard CustomerDataList into the QuickBooks BatchItemRequest and send the request to QBs*/
                if(!empty($custDataList->CustomerDataList))
                {
                    $batchRequest = new BatchItemRequest;
                    $arrayResponse = $batchRequest->SyncToQB($office, $custDataList);//Returns the BatchItemResponse JSON from QBs 
                    
                    array_push($requestObj, json_decode($batchRequest->EchoJson(),true)); //this will concatinate for display all of the batch request objects created
                          
                    $msg['requestCount'] = $msg['requestCount'] + count($batchRequest->BatchItemRequest); //total number of rows sent in batch request
                    
                    //////////////////////////////////////////////////////////*QuickBooks_BatchItemResponse ...i.e. Sync updated QBs data back to BC*/////////////////////////////////////////
                    /*Create a BatchItemResponse object, load the QBs JSON response into it, and Update the BlueCard customer records with the newly generated QBs IDs*/
                    $batchResponse = new BatchItemResponse;	
                    $batchResponse->DeserializeObject($arrayResponse);//This is deserializing an array not an object 
		
					 /*Send the BatchItemResponse from QBs to BC to update the BlueCard records*/
                    $responseCount = $batchResponse->Update(); //function returns an array containing number of valid records returned and number of faults
                
                    $msg['responseCount']['valid'] = $msg['responseCount']['valid'] + $responseCount['valid'];
                    $msg['responseCount']['fault'] = $msg['responseCount']['fault'] + $responseCount['fault'];
                }   
                $startposition = $startposition + 30;//this integer must match the intiger dividing total count and "LIMIT" in the GetCustomerDataList_Pagination() query
                unset($custDataList,$batchRequest,$batchResponse);//clears out the variables for the next iteration 
            }   
        }       
        
        $msg['requestObj'] = $requestObj;
        
        /*Update the Sync timestamp on the Office object*/
        Office::sync_timestamp_update($office->office_id);
        
        //Log the sync
        QLog::sync(json_encode($msg, JSON_PRETTY_PRINT));
        QLog::sync(QuickbooksOnline::requests(5));
        //$msg['quickbooks_report'] = QuickbooksOnline::report();
        //$msg['success'] = true;

        return $msg;
    }
    public static function test_connection()
    {
        return QuickbooksOnline::test_connection(Auth::user()->qbo_membership->office);
    }
    public static function webhooks(Request $request)
    {

        //decode json content into php array
        $data = json_decode($request->getContent(), true);

        //Log the webhook if the app is in 'debug' mode
        if(config('app.debug') == true)
        {
            QLog::webhook(json_encode($data));
        }     
		
		/*NOTE: If the Sync writes records back to QuickBooks then the WebHooks will be triggered again, this loop will continue until
		there are no newly updated/created records in BlueCard to be written back to QuickBooks on the Sync*/
		
		foreach($data['eventNotifications'] as $event)
		{
            $office = Office::by_realmid($event['realmId']);
            if($office != null)
            {
                Self::sync($office);
            }			
		}
        
		//Self::sync($data['eventNotifications'][0]['realmId']);
    }


    //Unused Code
    /*
    public static function customer_count_from_quickbooks()
    {
        return QBCustomer::count(Auth::user()->qbo_membership->office);
    }
    public static function customer_update_count_from_quickbooks()
    {
        return QBCustomer::get_update_count("2017-03-12T15:58:08.976-07:00");
    }  
    public static function customer_update_list_from_quickbooks()
    {
        //capture the sync_timestamp as a DateTime object;
        $dt = Office::sync_timestamp_get(Auth::user()->qb_realmid);
        //convert the DateTime into a quickbooks formatted timestamp.
        $qbTimestamp = DateTimeConverter::datetime_to_quickbooks_timestamp($dt);
        return QbCustomer::get_update_list_from_quickbooks($qbTimestamp); 
    }
    public static function find($id)
    {
        $office = Auth::user()->qbo_membership->office;
        return QBCustomer::find($office, $id);
    }
    public static function office_load()
    {
        if(empty(Office::office_id_get(Auth::user()->qb_realmid)))
        {
            $data = 
            [
                [
                'officename'=>Auth::user()->name, 'officeaddress1'=>Auth::user()->email, 'officecity'=>'unknown', 
                'officestate_id'=>State::where('statename','unknown')->value('state_id')->first(), 
                'officezipcode'=>'unknown', 'qb_realmid'=>Auth::user()->qb_realmid,
                ],
            ];
            return Office::insert($data); 
        }
        return 0;
    }
    public static function paymentmethod_load()
    {
        //Get all payment methods from quickbooks
        $QBPaymentMethodList = QBPaymentMethod::all();

        //iterate through the qbCustomer list
        foreach($QBPaymentMethodList as $qbPaymentMethod)
        {
            //Convert each qbPaymentMethod into a PaymentMethod object and create in database
            $p = $qbPaymentMethod->to_paymentmethod();
            $pm = PaymentMethod::create($p->toArray());
            unset($qbPaymentMethod);
        }
        
        unset($QBPaymentMethodList);
 
        //return $PaymentMethodList;      
        return PaymentMethod::all();  
    }
    public static function reconnect(Office $office)
    {
        $response = QuickbooksOnline::reconnect($office);

        if($response['ErrorCode'] == 0)
        {

            Office::session()->reset_oauth($response['OAuthToken'], $response['OAuthTokenSecret']);

        }
        return $response;
    }
    public static function report()
    {
        return QuickbooksOnline::report();
    }    
    public static function sync_timestamp_from_office()
    {
        return Office::sync_timestamp_get(Auth::user()->qb_realmid);
    }
    public static function update()
    { 
        return "Function not built yet.";


        //This function updates Quickbooks with a list of CustomerData...returns boolean.

        //get a list of CustomerData updated since the office->sync_timestamp.
        $cdList = CustomerData::update_list_get();
        return $cdList;

        //convert the customerdata list into a list of QBCustomers
        $qbList = [];
        foreach($cdList as $cd)
        {
            $qbList[] = QBCustomer::from_customerdata($cd);
        }

        //create a list of BatchItems
        $BatchItemRequest = [];
        foreach($qbList as $qbCustomer)
        {
            $BatchItemRequest[] = QBBatchItem::from_qbCustomer($qbCustomer);
        }




        //NOTE: DO NOT SEND a CustomerDataList JSON string with more than 30 CustomerData records at a time!
        
        //////////////////////////////////////////////////////////*QuickBooks_BatchItemRequest ...i.e. SyncCustDataToQB /////////////////////////////////////////////
        //Create a CustomerDataList and load it with each set of customers we want to send to QuickBooks.
        //QuickBooks will only allow a maximum of 30 CustomerData records sent per batch request.


        $custDataList = new CustomerDataList;
        $custDataList->DeserializeJson($json);
            
        //Map the BlueCard CustomerDataList into the QuickBooks BatchItemRequest and send the request to QBs
        $batchRequest = new BatchItemRequest;
        $jsonResponse = $batchRequest->SyncToQB($custDataList);//Returns the BatchItemResponse JSON from QBs 
        
        $requestObj = array();
        array_push($requestObj, json_decode($batchRequest->EchoJson(),true)); //this will concatinate for display all of the batch request objects created
              
        $msg['requestCount'] = $msg['requestCount'] + count($batchRequest->BatchItemRequest); //total number of rows sent in batch request
        
        //////////////////////////////////////////////////////////*QuickBooks_BatchItemResponse ...i.e. Sync updated QBs data back to BC/////////////////////////////////////////
        //Send the BatchItemResponse from QBs to BC to update the BlueCard records
        //Create a BatchItemResponse object, load the QBs JSON response into it, and Update the BlueCard customer records with the newly generated QBs IDs
        $batchResponse = new BatchItemResponse;
        $batchResponse->DeserializeJson($jsonResponse);
        $responseCount = $batchResponse->Update(); //function returns an array containing number of valid records returned and number of faults
    
        $msg['responseCount']['valid'] = $msg['responseCount']['valid'] + $responseCount['valid'];
        $msg['responseCount']['fault'] = $msg['responseCount']['fault'] + $responseCount['fault'];
            
        $msg['requestObj'] = $requestObj;
        
        //Update the Sync timestamp on the Office object.
        $office = new Office;
        $office->SyncTimeStamp_Update(Auth::user()->office_id);
        
        return $msg;    
    }
    */
        
}