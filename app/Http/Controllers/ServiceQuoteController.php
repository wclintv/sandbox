<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceQuote;

class ServiceQuoteController extends Controller
{
	public static function all()
	{
		return ServiceQuote::with('serviceitem','frequency','priceitem','servicetime')->where('servicequote_id','>',0)->get();
	}	
    public static function create(Array $data)
    {
    	$sq = ServiceQuote::create($data);	
		return ServiceQuote::find($sq->servicequote_id);
	}
	public static function find($servicequote_id)
	{
		return ServiceQuote::with('serviceitem','frequency','priceitem','servicetime')->where('servicequote_id',$servicequote_id)->get();
	}
	public static function delete($servicequote_id)
	{ 
		return "not built yet";
	}	
	public static function update(Array $data){
		$sq = ServiceQuote::update($data);
		return ServiceQuote::find($sq->servicequote_id);
	}
}
