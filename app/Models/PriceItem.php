<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceItem extends Model
{
	protected $table = 'priceschedule';
	protected $primaryKey = 'priceschedule_id';
	protected $fillable = [
		'priceschedule_id',
		'schedule',
		'prilettergrade_id',
		'pricequote_id',
	];
	public function housecode()
	{
		return $this->hasOne('App\Models\Housecode','lettergrade_id','prilettergrade_id');
	}
	public function price()
	{
		return $this->hasOne('App\Models\Price','price_id','pricequote_id');
	}
}
