<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentMethod;

class Appointment extends Model
{
    protected $table = 'appointment';
    protected $primaryKey = 'appointment_id';
    protected $fillable =
    [
        'appointment_id',
        'aptcust_id',
        'aptaddress_id',
        'apttype_id',
        'aptstatus_id',
        'estimator_id',
        'aptserviceday_id',
        'serviceday',
        'aptstart_datetime',
        'aptend_datetime',
        'aptreferredby_id',
        'aptcancelby_id',
        'aptpaymentmethod_id',
        'billed',
        'aptqbsync',
        'aptisactive',        
    ];


    public function address()
    {
        return $this->hasOne('App\Models\Address','address_id','aptaddress_id');
    }
    public function cancelby()
    {
        return $this->hasOne('App\Models\CancelBy','cancelby_id','aptcancelby_id');
    }
    public function customer()
    {
        return $this->hasOne('App\Models\Customer','cust_id','aptcust_id');
    }
    public function estimator()
    {
        return $this->hasOne('App\Models\Employee','employee_id','estimator_id');
    }
    public function paymentmethod()
    {
        return $this->hasOne('App\Models\PaymentMethod','paymentmethod_id','aptpaymentmethod_id');
    }
    public function referredby()
    {
    	return $this->hasOne('App\Models\ReferredBy','referredby_id','aptreferredby_id');
    }
    public function serviceday()
    {
        return $this->hasOne('App\Models\ServiceDay','serviceday_id','aptserviceday_id');
    }
    public function status()
    {
        return $this->hasOne('App\Models\AppointmentStatus','appointmentstatus_id','aptstatus_id');
    }
    public function type()
    {
        return $this->hasOne('App\Models\AppointmentType','appointmenttype_id','apttype_id');
    }


    //Mutators / Accessors
    public function setAptpaymentmethodIdAttribute($value)//How does this get called by the Update function???
    {
		if(!empty($value))
		{	
            $this->attributes['aptpaymentmethod_id'] = (integer)($value);  
		} 
        else 
		{
			$id = PaymentMethod::where('paymentoption','unknown')->value('paymentmethod_id');
			$this->attributes['aptpaymentmethod_id'] = $id[0];
		}
    }
}




