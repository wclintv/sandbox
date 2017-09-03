<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PathfinderController extends Controller
{
    public function income()
    {
    	return view('pages.pathfinder');
    }
}
