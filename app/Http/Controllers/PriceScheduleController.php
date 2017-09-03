<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PriceSchedule;

class PriceScheduleController extends Controller
{
	public static function all()
    {
    	return PriceSchedule::all();
    }
    public static function find($priceschedule_id)
    {
    	return PriceSchedule::find($priceschedule_id);
    }
    
}
