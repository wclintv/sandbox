<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Events\LogoutEvent;

class AuthController extends Controller
{
	// Authentication Routes
	public function showLoginForm(Request $request)
	{
		return response('SnapDsk uses your Intuit account to sign in.  Please sign in with Intuit.', 404);
	}
	public function login(Request $request)
	{
		return view('auth.login');
	}
    public function logout(Request $request)
    {
        $user = Auth::user();

    	Auth::logout();
    	return view('pages.index');
    }


    // Registration Routes
    public function register(Request $request)
    {
    	return response('SnapDsk uses your Intuit account to register users.  Please login with Intuit.', 404);
    }
    public function showRegistrationForm(Request $request)
    {
    	return response('SnapDsk uses your Intuit account to register users.  Please login with Intuit.', 404);
    }


	// Password Reset Routes...
    public function showLinkRequestForm(Request $request)
    {
		return response('SnapDsk uses your Intuit account to reset passwords.  Please login with Intuit.', 404);
    }
    public function sendResetLinkEmail(Request $request)
    {
		return response('SnapDsk uses your Intuit account to reset passwords.  Please login with Intuit.', 404);
    }
    public function showResetForm(Request $request, $token)
    {
    	return response('SnapDsk uses your Intuit account to reset passwords.  Please login with Intuit.', 404);	
    }
    public function reset(Request $request)
    {
    	return response('SnapDsk uses your Intuit account to reset passwords.  Please login with Intuit.', 404);
    }

}
