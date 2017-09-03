<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeamArea;

class TeamAreaController extends Controller
{
	public static function all()
    {
    	return TeamArea::all();
    }
    public static function find($teamarea_id)
    {
    	return TeamArea::find($teamarea_id);
    }
    
}
