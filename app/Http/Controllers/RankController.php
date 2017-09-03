<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rank;

class RankController extends Controller
{
	public static function all()
    {
    	return Rank::all();
    }
    public static function find($rank_id)
    {
    	return Rank::find($rank_id);
    }
    
}
