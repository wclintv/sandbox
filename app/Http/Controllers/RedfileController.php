<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Redfile;

class RedfileController extends Controller
{
	public static function all()
    {
    	return Redfile::all();
    }
    public static function find($redfile_id)
    {
    	return Redfile::find($redfile_id);
    }
    
}
