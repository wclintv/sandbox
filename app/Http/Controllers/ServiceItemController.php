<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceItem;

class ServiceItemController extends Controller
{
	public static function all()
    {
    	return ServiceItem::all();
    }
    public static function find($serviceitem_id)
    {
    	return ServiceItem::find($serviceitem_id);
    }
    
}