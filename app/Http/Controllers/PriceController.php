<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Price;

class PriceController extends Controller
{
	public static function all()
    {
    	return Price::all();
    }
    public static function find($price_id)
    {
    	return Price::find($price_id);
    }
    
}
