<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;
use App\Models\Address;

class Customer extends Model
{
    protected $billingstate;
    protected $suffix;
    protected $table = 'customer';
    protected $primaryKey = 'cust_id';
    protected $fillable = [
    'altphone',
    'balancedue',
    'billingaddress1',
    'billingaddress2',
    'billingcity',
    'billingfirstname',
    'billinglastname',
    'billingstate_id',
    'billingzipcode',
    'company',
    'cust_id',
    'custsuffix_id',
    'email',
    'fax',
    'firstname',
    'lastname',
    'middlename',
	'mobilephone',
    'parentqbid',
    'phone',
    'qbcustisactive',
    'qbeditsequence',
    'qbenddate',
    'qbid',
    'qbstartdate',
    'qbsynctoken',
    'qbtimemodified',
    'redfile',
    'resalenum',
    'seperatebillingaddress',
    'suffix',
    'title',
    'website',
    ];


	public function billingstate()
	{
	    return $this->hasOne('App\Models\State','state_id','billingstate_id');
	}
	public function suffix()
	{
		return $this->hasOne('App\Models\Suffix','suffix_id','custsuffix_id');	
	}
    public function appointments()
    {
        return $this->hasMany('App\Models\Appointment', 'aptcust_id', 'cust_id');
    }
    public function addresses()
    {
        //$id = Appointment::where('aptcust_id', $this->cust_id)->value('aptaddress_id');
		//return Address::with('state','office','teamarea')->where('address_id',$id)->get();
	
		return $this->hasManyThrough('App\Models\Address', 'App\Models\Appointment', 'aptaddress_id', 'address_id');
    }
    public function servicequotes()
    {
        return $this->hasManyThrough('App\Models\ServiceQuote', 'App\Models\Appointment', 'appointment_id', 'qteappointment_id');
    }
    public function full_name()
    {
        $result = null;

        if($this->firstname)
        {
            $result .= $this->firstname . ' ';
        }
        if($this->middlename)
        {
            if($this->middlename != "unknown")
            {
                $result .= $this->middlename . ' ';
            }            
        }
        if($this->lastname)
        {
            $result .= $this->lastname;
        }
        if($this->suffix != null)
        {
            $result .= ', ' . $this->suffix;
        }
        return $result;
    }
    public function json_pretty()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    //Mutators
    public function setSeperatebillingaddressAttribute($value)
    {
        $this->attributes['seperatebillingaddress'] = (integer)($value);
    }
    public function getSeperatebillingaddressAttribute($value)
    {
        return (boolean)($value);
    }


    public function getBalancedueAttribute($value)
    {
        if(is_double($value))
        {
            return "$" . number_format($value,2);
        }
        return $value;        
    }
}