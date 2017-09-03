<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceItem;
use App\Models\Frequency;
use App\Models\PriceItem;
use App\Models\ServiceTime;

class ServiceQuote extends Model
{
    protected $table = 'servicequote';
    protected $primaryKey = 'servicequote_id';
    protected $fillable =
    [
    	'servicequote_id',
    	'qteappointment_id',
    	'qteserviceitem_id',
    	'qtefreq_id',
    	'qtepriceschedule_id',
    	'qteservicetime_id',
    	'fixedservicetime',
    	'fixedservicedate',
    	'fixedservicetime',
    	'firstservicedate',
    	'lastservicedate',
    	'notes',
    	'qteqbsync',
    	'qteisactive',
    	'qteisarchive',
    ];


	public function serviceitem()
	{
		$s = $this->hasOne('App\Models\ServiceItem','serviceitem_id','qteserviceitem_id');
		if($s != null)
		{
				return $s;	
		}
		return ServiceItem::newInstance();
	}
	public function frequency()
	{
		return $this->hasOne('App\Models\Frequency','freq_id','qtefreq_id');
	}
	public function priceitem()
	{
		return $this->hasOne('App\Models\PriceSchedule','priceschedule_id','qtepriceschedule_id');
	}
	public function servicetime()
	{
		return $this->hasOne('App\Models\ServiceTime','servicetime_id','qteservicetime_id');
	}
	public function appointments()
	{
		return $this->belongsToMany('App\Models\Appointment','appointment_id','qteappointment_id');
	}
} 
