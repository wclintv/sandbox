<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerData;
use App\Models\Customer;
use App\Models\CustomerSearchData;
use App\Models\ApiError;
use Auth;

class CustomerDataController extends Controller
{
	//Web Handling - Returns HTML Views
	public function index()
	{
		$customers = Customer::paginate(10);
		return view('customers.index')->with('customers', $customers);
	}
	public function show(Request $request)
	{ 
		$cd = CustomerData::find($request['param']);
		if($cd != null)
		{
			return view('customers.show')->with('customerdata', $cd);
			echo('object is cd');
		}
		return view('customers.show')->with('apierror', new ApiError('CUSTOMER_NOT_FOUND', 416));	
	}
	public function edit(Request $request)
	{
		$customerdata = CustomerData::find($request['param']);
		return view('customers.edit')->with('customerdata', $customerdata);
	}
	public function show_create()
	{
		return view('customers.create');
	}
	public function store(Request $request)
	{
		$customerdata = new CustomerData;
		$customerdata->get($request['param']);
		$customerdata->fill($request->toArray());
		$customerdata->Save();
		return view('customers.show')->with('customerdata', $customerdata);
	}

	//Api Handling - Returns an object; 
	public static function all()
	{
		return CustomerData::all();
	}   
	public static function create(Array $data)
	{
		$cd = CustomerData::create($data);
		return CustomerData::find($cd->Customer->cust_id);
	}
	public static function delete($cust_id)
	{
		return "Function not built yet";
	}
	public static function deleteall($office_id)
	{
		return CustomerData::deleteall($office_id);
	}	
	public static function find($cust_id)
	{
		$cd = CustomerData::find($cust_id);
		if ($cd != null)
		{
			return $cd;
		}
		else
		{
			return new ApiError("CUSTOMER_NOT_FOUND", 416);
		}
	}
	public static function update(Array $data)
	{		
		$customerdata = CustomerData::find($data['Customer']['cust_id']);		
		$customerdata->fill($data);
		$customerdata->save();

		$customersearchdata = CustomerSearchData::find($data['Customer']['cust_id']);
		$customersearchdata->fill_from_customerdata($customerdata);
		$customersearchdata->save();

		return CustomerData::find($customerdata->Customer->cust_id);	
	}
	public static function update_list_get()
	{
		return CustomerData::update_list_get(Auth::user()->qbo_membership->office);
	}

}
