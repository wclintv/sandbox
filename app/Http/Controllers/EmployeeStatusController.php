<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeStatus;

class EmployeeStatusController extends Controller
{  
	public static function all()
	{
		return EmployeeStatus::all();
	}
	public static function find($employeestatus_id)
	{
		return EmployeeStatus::find($employeestatus_id);
	}
	
}
