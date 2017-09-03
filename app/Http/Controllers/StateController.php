<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;

class StateController extends Controller
{
	public static function all()
    {
    	return State::all();
    }
    public static function find($state_id)
    {
    	return State::find($state_id);
    }
    
}
