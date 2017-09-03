<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiResponse;
use App\Models\ApiError;
use App\User;
use Auth;


class ApiController extends Controller
{
    //Constructor
	public function __construct() 
	{
    	//include the current authentication module containing the authenticated user.
		$this->middleware('auth');
		$this->user = Auth::user();
	} 

	//Methods
	public function client(Request $request)
	{
		return view('pages.client')->with('methods', $this->methods());
	}
	private function get_response($method, $param, $json)
	{
		$response = new ApiResponse;

		if($this->validate_method($method))
		{ 
			if($this->validate_json($json))
			{
				$data = json_decode($json, true);
				switch($method)
				{
				/*1*/	case "Address.all":
							$response->Type = "AddressList";
							$response->Data = AddressController::all();
							break;
				/*2*/	case "Address.create":
							$response->Type = "Address";
							$response->Data = AddressController::create($data);
							break;
				/*3*/	case "Address.delete":
							$response->Type = "Boolean";
							$response->Data = AddressController::delete($param);
							break;
				/*4*/	case "Address.find":
							$response->Type = "Address";
							$response->Data = AddressController::find($param);
							break;
				/*5*/	case "Address.update":
							$response->Type = "Address";
							$response->Data = AddressController::update($data);
							break;	
				/*6*/	case "Appointment.all":
							$response->Type = "AppointmentList";
							$response->Data = AppointmentController::all();
							break; 								
				/*7*/	case 'Appointment.create':
							$response->Type = "Appointment";
							$response->Data = AppointmentController::create($data);
							break;
				/*8*/	case "Appointment.delete":
							$response->Type = "Boolean";
							$response->Data = AppointmentController::delete($param);
							break;
				/*9*/	case "Appointment.find":
							$response->Type = "Appointment";
							$response->Data = AppointmentController::find($param);
							break;
				/*10*/	case "Appointment.update":
							$response->Type = "Appointment";
							$repsonse->Data = AppointmentController::update($data);
							break;	
				/*11*/	case "AppointmentStatus.all":
							$response->Type="Appointment_Get";
							$response->Data = AppointmentStatusController::all();
							break;												
				/*12*/	case "AppointmentStatus.find":
							$response->Type ="AppointmentStatus";
							$response->Data = AppointmentStatusController::find($param);
							break;
				/*13*/	case "AppointmentType.all":
							$response->Type = "AppointmentType";
							$response->Data = AppointmentTypeController::all();
							break;
				/*14*/	case "AppointmentType.find":
							$response->Type = "AppointmentType";
							$response->Data = AppointmentTypeController::find($param);
							break;
				/*15*/	case "CancelBy.all":
							$response->Type="CancelByList";
							$response->Data = CancelByController::all();
							break;
				/*16*/	case "CancelBy.find":
							$response->Type="CancelBy";
							$response->Data = CancelByController::find($param);
							break;								
				/*17*/	case "Customer.all":
							$response->Type = "CustomerList";
							$response->Data = CustomerController::all();
							break;		
				/*18*/	case "Customer.create":
							$response->Type="Customer";
							$response->Data = CustomerController::create($data);
							break;														
				/*19*/	case "Customer.delete":
							$response->Type="Customer";
							$response->Data = CustomerController::delete($param);
							break;
				/*20*/	case "Customer.find":
							$response->Type="Customer";
							$response->Data = CustomerController::find($param);
							break;
				/*21*/	case "Customer.update":
							$response->Type="Customer";
							$response->Data = CustomerController::update($data);
							break;
				/*22*/	case "CustomerData.all":
							$response->Type = "CustomerDataList";
							$response->Data = CustomerDataController::all();
							break;
				/*23*/	case "CustomerData.create":
							$response->Type="CustomerData";
							$response->Data = CustomerDataController::create($data);
							break;
				/*24*/	case "CustomerData.delete":
							$response->Type="CustomerData";
							$response->Data = CustomerDataController::delete($param);
							break;
						case "CustomerData.deleteall":
							$response->Type="CustomerData";
							$response->Data = CustomerDataController::deleteall($param);
							break;			
				/*25*/	case "CustomerData.find":
							$response->Type="CustomerData";
							$response->Data = CustomerDataController::find($param);
							break;
				/*26*/	case "CustomerData.update":
							$response->Type="CustomerData";
							$response->Data = CustomerDataController::update($data);
							break;
						case "CustomerData.update_list":
							$response->Type="CustomerDataList";
							$response->Data = CustomerDataController::update_list_get();
							break;
				/*27*/	case "CustomerSearchData.all":
							$response->Type = "CustomerSearchDataList";
							$response->Data = CustomerSearchDataController::all();
							break;
				/*28*/	case "CustomerSearchData.find":
							$response->Type = "CustomerSearchData";
							$response->Data = CustomerSearchDataController::find($param);
							break;
						case "CustomerSearchData.deleteall":
							$response->Type = "CustomerSearchData";
							$response->Data = CustomerSearchDataController::deleteall($param);
							break;	
				/*29*/	case "Database.create":
							$response->Type="Database.create";		
							$response->Data = DatabaseController::create();		
							break;
				/*30*/	case "Database.delete":
							$response->Type="Database.delete";							
							$response->Data = DatabaseController::delete();
							break;
				/*31*/	case "Database.load_defaults":
							$response->Type="Database.load_defaults";
							$response->Data = DatabaseController::load_defaults();			
							break;
				/*32*/	case "Employee.all":
							$response->Type = "EmployeeList";
							$response->Data = EmployeeController::all();
							break;
				/*33*/	case "Employee.create":
							$response->Type = "Employee";
							$response->Data = EmployeeController::create($data);
							break;
				/*34*/	case "Employee.delete":
							$response->Type = "Boolean";
							$response->Data = EmployeeController::delete($param);
							break;					
				/*35*/	case "Employee.find":
							$response->Type = "Employee";
							$response->Data = EmployeeController::find($param);
							break;
				/*36*/	case "Employee.update":
							$response->Type = "Employee";
							$response->Data = EmployeeController::update($data);
							break;								
				/*37*/	case "EmployeePosition.all":
							$response->Type="EmployeePositionList";
							$response->Data = EmployeePositionController::all();
							break;
				/*38*/	case "EmployeePosition.find":
							$response->Type="EmployeePosition";
							$response->Data = EmployeePositionController::find($param);
							break;						
				/*39*/	case "EmployeeStatus.all":
							$response->Type="EmployeeStatusList";
							$response->Data= EmployeeStatusController::all();
							break;
				/*40*/	case "EmployeeStatus.find":
							$response->Type="EmployeeStatus";
							$response->Data= EmployeeStatusController::find($param);
							break;
				/*41*/	case "Frequency.all":
							$response->Type="FrequencyList";
							$response->Data= FrequencyController::all();
							break;
				/*42*/	case "Frequency.find":
							$response->Type="Frequency";
							$response->Data= FrequencyController::find($param);
							break;
				/*43*/	case "Housecode.all":
							$response->Type="HousecodeList";
							$response->Data= HousecodeController::all();
							break;
				/*44*/	case "Housecode.find":
							$response->Type="Housecode";
							$response->Data= HousecodeController::find($param);
							break;
				/*45*/	case "Keylock.all":
							$response->Type="KeylockList";
							$response->Data= KeylockController::all();
							break;
				/*46*/	case "Keylock.find":
							$response->Type="Keylock";
							$response->Data= KeylockController::find($param);
							break;
				/*47*/	case "Office.all":
							$response->Type="OfficeList";
							$response->Data= OfficeController::all();
							break;
				/*48*/	case "Office.create":
							$response->Type="OfficeList";
							$response->Data= OfficeController::create($data);
							break;
				/*49*/	case "Office.delete":
							$response->Type="OfficeList";
							$response->Data= OfficeController::delete($param);
							break;
				/*50*/	case "Office.find":
							$response->Type="Office";
							$response->Data= OfficeController::find($param);
							break;
				/*51*/	case "Office.update":
							$response->Type="OfficeList";
							$response->Data= OfficeController::update($data);
							break;
				/*52*/	case "PaymentMethod.all":
							$response->Type="PaymentMethodList";
							$response->Data= PaymentMethodController::all();
							break;
				/*53*/	case "PaymentMethod.find":
							$response->Type="PaymentMethod";
							$response->Data= PaymentMethodController::find($param);
							break;
				/*54*/	case "Price.all":
							$response->Type="PriceList";
							$response->Data= PriceController::all();
							break;
				/*55*/	case "Price.find":
							$response->Type="Price";
							$response->Data= PriceController::find($param);
							break;
				/*56*/	case "PriceItem.all":
							$response->Type="PriceItemList";
							$response->Data= PriceItemController::all();
							break;
				/*57*/	case "PriceItem.find":
							$response->Type="PriceItem";
							$response->Data= PriceItemController::find($param);
							break;
				/*58*/	case "PriceSchedule.all":
							$response->Type="PriceScheduleList";
							$response->Data= PriceScheduleController::all();
							break;
				/*59*/	case "PriceSchedule.find":
							$response->Type="PriceSchedule";
							$response->Data= PriceScheduleController::find($param);
							break;
				/*60*/	case "PSchedule.find":
							$response->Type="PSchedule";
							$response->Data= PScheduleController::find($param);
							break;
				/*61*/	case "PSchedule.all":
							$response->Type="PScheduleList";
							$response->Data= PScheduleController::all();
							break;
				/*62*/	case "Quickbooks.Customer.all":
							$response->Type = "Quickbooks.Customer.all";
							$response->Data = QuickbooksController::all();
							break;
				/*63*/	case "Quickbooks.Customer.find":
							$response->Type = "Quickbooks.Customer.find";
							$response->Data = QuickbooksController::find($param);
							break;
						case "Quickbooks.Customer.count":
							$response->Type = "Quickbooks.Customer.count";
							$response->Data = QuickbooksController::customer_count_from_quickbooks();
							break;
				/*64*/	case "Quickbooks.Customer.update_count":
							$response->Type = "Quickbooks.Customer.update_count";
							$response->Data = QuickbooksController::customer_update_count_from_quickbooks();
							break;
				/*65*/	case "Quickbooks.Customer.update_list":
							$response->Type = "Quickbooks.Customer.update_list";
							$response->Data = QuickbooksController::customer_update_list_from_quickbooks();
							break;
				/*67*/	case "Quickbooks.Customer.load":
							$response->Type = "Quickbooks.Customer.load";
							$response->Data = QuickbooksController::customer_load();
							break;	
						case "Quickbooks.Office.load":
							$response->Type = "Quickbooks.Office.load";
							$response->Data = QuickbooksController::office_load();
							break;	
						case "Quickbooks.PaymentMethod.load":
							$response->Type = "Quickbooks.PaymentMethod.load";
							$response->Data = QuickbooksController::paymentmethod_load();
							break;		
				/*68*/	case "Quickbooks.sync":
							$response->Type = "Quickbooks.sync";
							$response->Sync = QuickbooksController::sync(Auth::user()->qbo_membership->office);
							break;
						case "Quickbooks.sync_timestamp_from_office":
							$response->Type = "Quickbooks.sync_timestamp_from_office";
							$response->Data = QuickbooksController::sync_timestamp_from_office();
							break;
				/*69*/	case "Quickbooks.test_connection":
							$response->Type="Quickbooks.test";
							$response->Data= QuickbooksController::test_connection();
							break;
						case "Quickbooks.webhooks":
							//need to figure out how to send $request as a type Request
							$request = 'json';
							$response->Type = "Quickbooks.webhooks";
							$response->Data = QuickbooksController::webhooks($request);
							break;	
				/*70*/	case "Rank.all":
							$response->Type="RankList";
							$response->Data= RankController::all();
							break;								
				/*71*/	case "Rank.find":
							$response->Type="Rank";
							$response->Data= RankController::find($param);
							break;
				/*72*/	case "Redfile.all":
							$response->Type="RedfileList";
							$response->Data= RedfileController::all();
							break;
				/*73*/	case "Redfile.find":
							$response->Type="Redfile";
							$response->Data= RedfileController::find($param);
							break;
				/*74*/	case "ReferredBy.all":
							$response->Type="ReferredByList";
							$response->Data= ReferredByController::all();
							break;
				/*75*/	case "ReferredBy.find":
							$response->Type="ReferredBy";
							$response->Data= ReferredByController::find($param);
							break;												
				/*76*/	case "SecurityPrivileges.all":
							$response->Type="SecurityPrivilegesList";
							$response->Data= SecurityPrivilegesController::all();
							break;
				/*77*/	case "SecurityPrivileges.find":
							$response->Type="SecurityPrivileges";
							$response->Data= SecurityPrivilegesController::find($param);
							break;								
				/*78*/	case "ServiceDay.all":
							$response->Type="ServiceDayList";
							$response->Data= ServiceDayController::all();
							break;
				/*79*/	case "ServiceDay.find":
							$response->Type="ServiceDay";
							$response->Data= ServiceDayController::find($param);
							break;													
				/*80*/	case "ServiceItem.all":
							$response->Type="ServiceItemList";
							$response->Data= ServiceItemController::all();
							break;		
				/*81*/	case "ServiceItem.find":
							$response->Type="ServiceItem";
							$response->Data= ServiceItemController::find($param);
							break;	
				/*82*/	case "ServiceQuote.all":
							$response->Type="ServiceQuoteList";
							$response->Data= ServiceQuoteController::all();
							break;	
				/*83*/	case "ServiceQuote.create":
							$response->Type="ServiceQuote";
							$response->Data= ServiceQuoteController::create($data);
							break;	
				/*84*/	case "ServiceQuote.delete":
							$response->Type="Boolean";
							$response->Data= ServiceQuoteController::delete($param);
							break;	
				/*85*/	case "ServiceQuote.find":
							$response->Type="ServiceQuote";
							$response->Data= ServiceQuoteController::find($param);
							break;
				/*86*/	case "ServiceQuote.update":
							$response->Type="ServiceQuote";
							$response->Data= ServiceQuoteController::update($data);
							break;							
				/*87*/	case "ServiceTime.all":
							$response->Type="ServiceTimeList";
							$response->Data= ServiceTimeController::all();
							break;				
				/*88*/	case "ServiceTime.find":
							$response->Type="ServiceTime";
							$response->Data= ServiceTimeController::find($param);
							break;																
				/*89*/	case "State.all":
							$response->Type="StateList";
							$response->Data= StateController::all();
							break;	
				/*90*/	case "State.find":
							$response->Type="State";
							$response->Data= StateController::find($param);
							break;												
				/*91*/	case "Suffix.all":
							$response->Type="SuffixList";
							$response->Data= SuffixController::all();
							break;
				/*92*/	case "Suffix.find":
							$response->Type="Suffix";
							$response->Data= SuffixController::find($param);
							break;									
				/*93*/	case "TeamArea.all":
							$response->Type="TeamAreaList";
							$response->Data= TeamAreaController::all();
							break;	
				/*94*/	case "TeamArea.find":
							$response->Type="TeamArea";
							$response->Data= TeamAreaController::find($param);
							break;		
				}

				//Run the Sync function if the method was an Update, or Create.
				if($this->validate_sync($method) === true)
				{
					//$response->Sync(QuickbooksController::sync());	
				}
			}
			else
			{
				http_response_code(400);
				$response->Type = "Error";
				$response->Data = new ApiError($this->json_get_error($json), 400);
			}
		}
		else
		{
			http_response_code(404);
			$response->Type = "Error";
			$response->Data = new ApiError("Ack! The API didn't recognize that request!", 404);
		}	
		return $response;	
	}
	private function json_get_error($json)
	{
		$result = null;

		//must do json_decode() in order for json_last_error() to return any errors that occured
		$arr = json_decode($json,true);
		
		switch (json_last_error()) 
		{
			case JSON_ERROR_NONE:
			$result = 'No Errors Found';
			$result = null;
			break;
			case JSON_ERROR_DEPTH:
			$result = 'JSON Error - Maximum stack depth exceeded';
			break;
			case JSON_ERROR_STATE_MISMATCH:
			$result = 'JSON Error - Underflow or the modes mismatch';
			break;
			case JSON_ERROR_CTRL_CHAR:
			$result = 'JSON Error - Unexpected control character found';
			break;
			case JSON_ERROR_SYNTAX:
			$result = 'JSON Error - Syntax error, malformed JSON';
			break;
			case JSON_ERROR_UTF8:
			$result = 'JSON Error - Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
			default:
			$result = null;
			break;
		}

		return $result;
	}	
	private function methods()
	{
		$values = 
		[
		/*1*/		'Address.all',
		/*2*/		'Address.create',
		/*3*/		'Address.delete',		
		/*4*/		'Address.find',
		/*5*/		'Address.update',
		/*6*/		'Appointment.all',
		/*7*/		'Appointment.create',
		/*8*/		'Appointment.delete',		
		/*9*/		'Appointment.find',
		/*10*/		'Appointment.update',
		/*11*/		'AppointmentStatus.all',
		/*12*/		'AppointmentStatus.find',
		/*13*/		'AppointmentType.all',
		/*14*/		'AppointmentType.find',
		/*15*/		'CancelBy.all',
		/*16*/		'CancelBy.find',
		/*17*/		'Customer.all',
		/*18*/		'Customer.create',
		/*19*/		'Customer.delete',			
		/*20*/		'Customer.find',
		/*21*/		'Customer.update',
		/*22*/		'CustomerData.all',
		/*23*/		'CustomerData.create',
		/*24*/		'CustomerData.delete',
					'CustomerData.deleteall',
		/*25*/		'CustomerData.find',
		/*26*/		'CustomerData.update',
					'CustomerData.update_list',
		/*27*/		'CustomerSearchData.all',
		/*28*/		'CustomersearchData.find',
					'CustomerSearchData.deleteall',
		/*29*/		'Database.create',
		/*30*/		'Database.delete',
		/*31*/		'Database.load_defaults',
		/*32*/		'Employee.all',
		/*33*/		'Employee.create',
		/*34*/		'Employee.delete',
		/*35*/		'Employee.find',		
		/*36*/		'Employee.update',
		/*37*/		'EmployeePosition.all',
		/*38*/		'EmployeePosition.find',
		/*39*/		'EmployeeStatus.all',
		/*40*/		'EmployeeStatus.find',
		/*41*/		'Frequency.all',
		/*42*/		'Frequency.find',
		/*43*/		'Housecode.all',
		/*44*/		'Housecode.find',
		/*45*/		'Keylock.all',
		/*46*/		'Keylock.find',
		/*47*/		'Office.all',
		/*48*/		'Office.create',
		/*49*/		'Office.delete',
		/*50*/		'Office.find',
		/*51*/		'Office.update',
		/*52*/		'PaymentMethod.all',
		/*53*/		'PaymentMethod.find',
		/*54*/		'Price.all',
		/*55*/		'Price.find',
		/*56*/		'PriceItem.all',
		/*57*/		'PriceItem.find',
		/*58*/		'PriceSchedule.all',
		/*59*/		'PriceSchedule.find',
		/*60*/		'PSchedule.all',
		/*61*/		'PSchedule.find',

		/*62*/		'Quickbooks.Customer.all',
		/*63*/		'Quickbooks.Customer.find',
					'Quickbooks.Customer.count',
		/*64*/		'Quickbooks.Customer.update_count',
		/*65*/		'Quickbooks.Customer.update_list',
		/*67*/		'Quickbooks.Customer.load',
					'Quickbooks.Office.load',
					'Quickbooks.PaymentMethod.load',
		/*68*/		'Quickbooks.sync',
					'Quickbooks.sync_timestamp_from_office',		
		/*69*/		'Quickbooks.test_connection',
					'Quickbooks.webhooks',

		/*70*/		'Rank.all',
		/*71*/		'Rank.find',
		/*72*/		'Redfile.all',
		/*73*/		'Redfile.find',
		/*74*/		'ReferredBy.all',
		/*75*/		'ReferredBy.find',
		/*76*/		'SecurityPrivileges.all',
		/*77*/		'SecurityPrivileges.find',
		/*78*/		'ServiceDay.all',
		/*79*/		'ServiceDay.find',
		/*80*/		'ServiceItem.all',
		/*81*/		'ServiceItem.find',
		/*82*/		'ServiceQuote.all',
		/*83*/		'ServiceQuote.create',	
		/*84*/		'ServiceQuote.delete',			
		/*85*/		'ServiceQuote.find',
		/*86*/		'ServiceQuote.update',
		/*87*/		'ServiceTime.all',
		/*88*/		'ServiceTime.find',
		/*89*/		'State.all',
		/*90*/		'State.find',
		/*91*/		'Suffix.all',
		/*92*/		'Suffix.find',
		/*93*/		'TeamArea.all',
		/*94*/		'TeamArea.find',
		];
		return $values;
	}
	public function post(Request $request)
	{
		$ApiResponse = $this->get_response($request->method, $request->param, $request->json);
		$ApiResponse->Method = $request->method;
		$ApiResponse->Param = $request->param;
		$qbReport	= QuickbooksController::report();

		return view('pages.client')
		->with('ApiResponse', $ApiResponse)
		->with('qbReport', $qbReport)
		->with('methods', $this->methods());
	}	
	private function validate_json($json)
	{
		$result = true;

		if (!empty($json)) 
		{
			if($this->json_get_error($json) != null)
			{
				$result = false;
			}
		} 
		return $result;
	}	
	private function validate_method($method)
	{
		$result = false;
		foreach($this->methods() as $m)
		{
			if($m === $method)
			{
				$result = true;
			}
		}
		return $result;
	}
	private function validate_sync($method)
	{
		//Sync should only be instantiated on all Primary and Derived objects after every CRUD operation except Read; Sync should NOT be instantiated after CRUD on Global objects
		$result = false;
		if(strpos($method, "update"))
		{
			$result = true;
		}
		if(strpos($method, "create"))
		{
			$result = true;
		}
		return $result;
	}

}
