<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MediaPlayerController extends Controller
{
    public function video($filename)
    {
    	return view('pages.mediaplayer')->with('filename', $filename);
    }
}
