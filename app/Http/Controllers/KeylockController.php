<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keylock;

class KeylockController extends Controller
{
	public static function all()
    {
    	return Keylock::all();
    }
    public static function find($keylock_id)
    {
    	return Keylock::find($keylock_id);
    }
    
}
