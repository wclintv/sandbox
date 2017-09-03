<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeePosition;

class EmployeePositionController extends Controller
{
	public static function all()
    {
    	return EmployeePosition::all();
    }
	public static function find($employeeposition_id)
	{
		return EmployeePosition::find($employeeposition_id);
	}
    
}
