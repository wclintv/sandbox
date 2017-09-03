<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Housecode;

class HousecodeController extends Controller
{
	public static function all()
    {
    	return Housecode::all();
    }
    public static function find($lettergrade_id)
    {
    	return Housecode::find($lettergrade_id);
    }
    
}
