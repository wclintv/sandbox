<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceDay;

class ServiceDayController extends Controller
{
	public static function all()
    {
    	return ServiceDay::all();
    }
    public static function find($serviceday_id)
    {
    	return ServiceDay::find($serviceday_id);
    }
    
}
