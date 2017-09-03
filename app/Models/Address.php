<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\State;

class Address extends Model
{
    protected $table ='address';
    protected $primaryKey = 'address_id';
    protected $fillable = 
    [
        'address_id',
        'adrqbid',
        'adroffice_id',
        'adrteamarea_id',
        'address1',
        'address2',
        'city',
        'adrstate_id',
        'zipcode',
        'bed',
        'bath',
        'sqft',
        'keylock',
        'adrisbilling',
		'adrqbsync',
        'adrisactive',
        'directions',
    ];

    public function state()
    {
    	$s = $this->hasOne('App\Models\State','state_id','adrstate_id');
    	if($s != null)
    	{
    		return $s;
    	}
    	return new State;
    }
    public function office()
    {
    	return $this->hasOne('App\Models\Office','office_id','adroffice_id');
    }
    public function teamarea()
    {
    	return $this->hasOne('App\Models\TeamArea','teamarea_id','adrteamarea_id');
    }
}
