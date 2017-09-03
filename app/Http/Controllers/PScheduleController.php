<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PSchedule;

class PScheduleController extends Controller
{
	public static function all()
    {
    	return PSchedule::all();
    }
    public static function find($pschedule_id)
    {
    	return PSchedule::find($pschedule_id);
    }
    
}
