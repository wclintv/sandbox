<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Frequency;

class FrequencyController extends Controller
{
	public static function all()
	{
		return Frequency::all();
	}
    public static function find($freq_id)
	{
		return Frequency::find($freq_id);
	}

}
