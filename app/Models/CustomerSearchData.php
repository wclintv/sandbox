<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CustomerData;

class CustomerSearchData extends Model
{
    protected $table = 'customersearchdata';
    protected $primaryKey = 'cust_id';
    protected $fillable = [
    	'cust_id',
    	'address_id',
    	'office_id',
    	'firstname',
    	'lastname',
    	'phone',
    	'mobilephone',
    	'address1',    	
    ];

    public static function create(CustomerData $cd)
    {
        $c = new CustomerSearchData;
        $c->cust_id = $cd->Customer->cust_id;
        $c->address_id = $cd->AddressList[0]->address_id;
        $c->office_id = $cd->AddressList[0]->adroffice_id;
        $c->firstname = $cd->Customer->firstname;
        $c->lastname = $cd->Customer->lastname;
        $c->phone = $cd->Customer->phone;
        $c->mobilephone = $cd->Customer->mobilephone;
        $c->address1 = $cd->AddressList[0]->address1;
        $c->save();
    }
	public static function deleteall($officeID)
	{
		return CustomerSearchData::where('office_id', $officeID)->delete();
	}
    public function fill_from_customerdata(CustomerData $cd)
    {
        $this->cust_id = $cd->Customer->cust_id;
        $this->address_id = $cd->AddressList[0]->address_id;
        $this->office_id = $cd->AddressList[0]->adroffice_id;
        $this->firstname = $cd->Customer->firstname;
        $this->lastname = $cd->Customer->lastname;
        $this->phone = $cd->Customer->phone;
        $this->mobilephone = $cd->Customer->mobilephone;
        $this->address1 = $cd->AddressList[0]->address1;
		
		return $this;
    }
}
