<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerSearchData;

class CustomerSearchDataController extends Controller
{
	public static function all()
	{
		return CustomerSearchData::all();
	}
	public static function create(Array $data)
	{
		CustomerSearchData::create($data);
	}
	public static function deleteall($office_id)
	{
		return CustomerSearchData::deleteall($office_id);
	}
    public static function find($cust_id)
    {
    	return CustomerSearchData::find($cust_id);
    }
    public static function update(Array $data)
    {
    	CustomerSearchData::update($data);
    }

}
