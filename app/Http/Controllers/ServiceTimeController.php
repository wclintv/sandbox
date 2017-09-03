<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceTime;

class ServiceTimeController extends Controller
{
	public static function all()
    {
    	return ServiceTime::all();
    }
    public static function find($servicetime_id)
    {
    	return ServiceTime::find($servicetime_id);
    }
    
}
