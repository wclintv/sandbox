<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
	public static function all()
	{
		return Address::all();
	}	
    public static function create(Array $data)
    {
    	$data = json_decode($json, true);
	
    	$adr = Address::create($data);
    	return Address::find($adr->address_id);
	}
	public static function delete($address_id)
	{
		return "Function not built yet";
	}		
	public static function find($address_id)
	{
		return Address::find($address_id);
	}
	public static function update(Array $data)
	{
		$id = $data['address_id'];
		$adr = Address::update($id, $data);
		CustomerSearchData::where('address_id',$id)->update();
		return Address::find($adr->address_id);
	}


}
