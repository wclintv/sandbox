<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Log;
use App\Utility\QLog;
use App\DataTables\CustomersDataTable;
use App\Jobs\QbOnlineSync;
use App\Models\Address;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\CustomerSearchData;
use App\Models\Office;
use App\Models\PaymentMethod;
use App\Models\ServiceQuote;
use App\Models\State;

class CustomerController extends Controller
{
	//Web Handling - returns and processes html views
	public static function create()
	{
		return view('customers.create');
	}
	public static function datatable(CustomersDataTable $dataTable)
	{
		return $dataTable->render('customers.datatable');
	}	
	public static function edit($cust_id)
	{
		$c = Customer::findOrFail($cust_id);
		return view('customers.edit')
			->with('cust_id', $c->cust_id)
			->with('customer_fullname', $c->full_name());
	}
	public static function index()
	{
		//$customers = Customer::paginate(10);
		//return view('customers.index')->with('customers',$customers);
		//return view('customers.index');
		return view('customers.index');
	}
	public function store(Request $request)
	{
		QLog::output('store function');
		$this->validate_request($request, '');

		$data = json_decode($request->getContent(), true);
		
		$c = Customer::create($data);

		//bind realmid/office_id to the address object then create
		$data['addresses'][0]['adroffice_id'] = Auth::user()->qbo_membership->office->office_id;
		$a = Address::create($data['addresses'][0]);
		
		//bind relationships then create appointment
		$data['appointments'][0]['aptcust_id'] = $c->cust_id;
		$data['appointments'][0]['aptaddress_id'] = $a->address_id;
		$data['appointments'][0]['aptqbsync'] = 1;
		$data['appointments'][0]['aptisactive'] = 1;
		
		$ap = Appointment::create($data['appointments'][0]);

		//bind relationship then create service quote;
		$data['servicequotes'][0]['qteappointment_id'] = $ap->appointment_id;
		$data['servicequotes'][0]['qteqbsync'] = 1;
		$data['servicequotes'][0]['qteisactive'] = 1;
		ServiceQuote::create($data['servicequotes'][0]);

		//Log::info(json_encode($data, JSON_PRETTY_PRINT));

		//sync with quickbooks
		QuickbooksController::sync(Auth::user()->qbo_membership->office);
		
		//NOTE: Running the Sync twice on create will compensate for Quickbooks running an update within the first 60 sec of a new record being created
		//UPDATE NOTE: Running the sync twice does not appear to be fixing the problem.
		//QuickbooksController::sync();

		return Customer::with('addresses','appointments','servicequotes')->where('cust_id','=', $c->cust_id)->first();
	}	
	public static function show($id)
	{
		$customer = Customer::findOrFail($id);

		return view('customers.show')
			->with('customer', $customer)
			->with('customer_fullname', $customer->full_name());
	}
	public static function search(Request $request)
	{
		$searchtext = $request->get('term');

		$office_id = Auth::user()->qbo_membership->office->office_id;

		$customers = CustomerSearchData::where('office_id','=', $office_id)->where(function ($query) use ($searchtext) 
		{
			$query->where('firstname','LIKE','%' . $searchtext . '%')
				  ->orWhere('lastname','LIKE','%' . $searchtext . '%')
				  ->orWhere('phone','LIKE','%' . $searchtext . '%')
				  ->orWhere('mobilephone', 'LIKE','%' . $searchtext . '%')
				  ->orWhere('address1', 'LIKE','%' . $searchtext . '%');						
		})->limit(10)->get();

	 	return response()->json($customers);
	}
	public function update(Request $request, $cust_id)
	{
		$this->validate_request($request);

		//echo $request->json;
		$data = json_decode($request->getContent(), true);	

		$customer = Customer::findOrFail($cust_id);
		$customer->update($data);

		// foreach($customer->addresses as $address)
		// {
		// 	QLog::test($address->address1);
		// }


		$customer->addresses[0]->update($data['addresses'][0]);		
		$customer->appointments[0]->update($data['appointments'][0]);		
		$customer->servicequotes[0]->update($data['servicequotes'][0]);

		//NOTE: CustomerSearchData table is updated in the quickbooks sync method.
		
		//sync with quickbooks
		//dispatch(new QbOnlineSync());
		$response = QuickbooksController::sync(Auth::user()->qbo_membership->office);

		return Customer::with('addresses','appointments','servicequotes')->where('cust_id','=',$customer->cust_id)->first();
	}	
	public function validate_request(Request $request)
	{
		if($request != null)
		{
			return $this->validate($request,
			[
				'title'									=>	'max:25',
				'firstname'								=>	'required|max:35',
				'middlename'							=> 	'nullable|max:35',
				'lastname'								=>	'required:max:35',
				'suffix'								=> 	'max:10',
				'company'								=>	'max:35',
				'email'									=>	'nullable|email|unique:users',
				'phone'									=>	'required|max:14|regex:/[0-9]+(?:\.[0-9]+)?\b/',
				'mobilephone'							=>	'nullable|max:14|regex:/[0-9]+(?:\.[0-9]+)?\b/',
				'fax'									=> 	'nullable|max:14|regex:/[0-9]+(?:\.[0-9]+)?\b/',
				'website'								=>  'nullable|url',
				//'appointments.0.aptpaymentmethod_id'	=>	'numeric:max:2',
				'balancedue'							=>	'nullable',
				'billingaddress1'						=>	'required|max:45',
				'billingcity'							=> 	'required|max:35',
				'billingstate_id'						=> 	'required',
				'billingzipcode'						=>	'required|regex:/\b\d{5}\b/',
				'seperatebillingaddress'				=> 	'nullable',
				'addresses.0.address1'					=>	'required|max:45',
				'addresses.0.city'						=>  'required|max:35',
				'addresses.0.adrstate_id'				=>	'required|numeric',
				'addresses.0.zipcode'					=>	'required|regex:/\b\d{5}\b/',
				'servicequotes.0.notes'					=>	'nullable',
			],			
			[
				'phone.regex'							=>  'must be a valid phone number.',
				'mobilephone.regex'						=>  'must be a valid phone number.',
				'billingaddress1.required'				=>	'street is required.',
				'billingaddress1.max'					=>	'45 character maximum.',
				'billingcity.required'					=> 	'city is required.',
				'billingcity.required.max'				=> 	'45 character maximum.',
				'billingstate_id.required'				=> 	'state is required',
				'billingstate_id.numeric'				=> 	'must be an integer.',
				'billingzipcode.required'				=>	'zipcode is required.',
				'billingzipcode.regex'					=>	'must be a valid zipcode.',

				'addresses.0.address1.required'			=>	'street is required.',
				'addresses.0.address1.max'				=>	'45 character maximum.',
				'addresses.0.city.required'				=>  'city is required.',
				'addresses.0.city.max'					=>  '45 character maximum.',
				'addresses.0.adrstate_id.required'		=>	'state is required.',
				'addresses.0.adrstate_id.numeric'		=>	'must be an integer.',
				'addresses.0.zipcode.required'			=>	'zipcode is required.',
				'addresses.0.zipcode.regex'				=>	'must be a valid zipcode.',
			]);
		}
		return null;
	}

	//API Handling - return data objects only
	public static function all()
	{
		return Customer::all();
	}	
    public static function create_from_array(Array $data)
    {
		$customer = new Customer;
		$customer->fill($data);		
		return $customer->save();
	}
	public static function delete($cust_id)
	{
		return "Function not built yet";
	}	
	public static function find($cust_id)
	{
		$c = Customer::with('addresses','appointments','servicequotes','billingstate')->where('cust_id','=', $cust_id)->first();
	
		if($c != null)
		{
			return $c;	
		}
		return response()->json(['error' => 'Resource not found!'], 404);			
	}
	public static function update_from_array(Array $data)
	{
		$id = $data['cust_id'];
		Customer::update($id, $data);
		CustomerSearchData::update($id, $data);
		return Customer::find($customer->cust_id);
	}



}
