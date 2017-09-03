<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentStatus;

class AppointmentStatusController extends Controller
{
	public static function all()
	{
		return AppointmentStatus::all();
	}      
	public static function find($appointmentstatus_id)
	{
		return AppointmentStatus::find($appointmentstatus_id);
	}	
}
