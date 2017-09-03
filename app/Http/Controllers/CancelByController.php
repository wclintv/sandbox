<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CancelBy;

class CancelByController extends Controller
{
	public static function find($cancelby_id)
	{
		return CancelBy::find($cancelby_id);
	}
	public static function all()
	{
		return CancelBy::all();
	}    
}
