<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReferredBy;

class ReferredByController extends Controller
{
	public static function all()
    {
    	return ReferredBy::all();
    }
    public static function find($referredby_id)
    {
    	return ReferredBy::find($referredby_id);
    }
    
}
