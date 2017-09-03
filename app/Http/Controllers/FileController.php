<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function video($id)
    {
    	return view('pages.video')->with('id', $id);
    }
    public function image($filename)
    {
    	//return $filename;
    	$path = app_path('Media/img/' . $filename);
        return response()->file($path);
    }
}
