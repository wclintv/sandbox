<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public static function all()
    {
    	return Employee::all();
    }	
    public static function find($employee_id)
    {
    	return Employee::find($employee_id);
    }


}
