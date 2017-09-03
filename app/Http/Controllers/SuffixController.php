<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suffix;

class SuffixController extends Controller
{
    public static function find($suffix_id)
    {
    	return Suffix::find($suffix_id);
    }
    public static function all()
    {
    	return Suffix::all();
    }
}
