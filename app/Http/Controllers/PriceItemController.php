<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PriceItem;

class PriceItemController extends Controller
{
	Public static function all()
    {
    	return PriceItem::with('housecode','price')->where('priceschedule_id','>',0)->get();
    }
    public static function find($priceitem_id)
    {
    	return PriceItem::with('housecode','price')->find($priceitem_id);
    }
    
}
