<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Utility\QLog;


class ApplicationController extends Controller
{
	//Web Handling
    public function settings()
    {
        return view('application.settings');
    }
	public function show()
	{
		return view('application.show');
	}




	//Api Handling
    public function update(Request $request)
    {
    	//Qlog::test($request['terms_of_service']);

    	$a = Application::first();
    	$a->update($request->toArray());
    	return $a;

    }

    public function icon()
    {
        $path = app_path('Media//img/snapdsk_icon_120x120.png');
        return response()->file($path);
    }
}
