<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Plugins\QuickbooksOnline\QuickbooksOnline;
use View;

class PagesController extends Controller
{
	public function about_get()
	{
		$companyName = "Western Service Systems Inc";
		$isUserRegistered = true;
		
		$users = array("Renato","Eric","John","Samantha");
		
		return view('pages.about')
			->with("companyName", $companyName)
			->with("isUserRegistered", $isUserRegistered)
			->with("users",$users);
	}
	public function contact_get()
	{
		$companyName = "Western Service Systems Inc";
		$isUserRegistered = true;
		
		$users = array("Renato","Eric","John","Samantha");
		
		return view('pages.contact')
			->with("companyName", $companyName)
			->with("isUserRegistered", $isUserRegistered)
			->with("users",$users);
	}	
	public function dashboard()
	{
		return view('pages.dashboard');
	}		
	public function default_get()
	{
		return view('pages.default');
	}	
	public function terms_of_service()
	{
		//TODO::  create an 'Application' table in database to hold text for eula, privacy, about, contact, etc.
		return view('pages.terms_of_service')->with('terms_of_service', Application::terms_of_service());
	}
	public function index()
	{
		return view('pages.index');		
	}
	public function privacy_policy()
	{
		//return view('pages.privacy_policy');
		return view('pages.privacy_policy')->with('privacy_policy', Application::privacy_policy());
	}
	public function requirements_index()
	{
		return view('pages.requirements_index');
	}
	public function requirements_marketing()
	{
		return view('pages.requirements_marketing');
	}
	public function requirements_security()
	{
		return view('pages.requirements_security');
	}
	public function requirements_technical()
	{
		return view('pages.requirements_technical');
	}
	public function settings()
	{
		return view('pages.settings');
	}
	public function support()
	{
		return view('pages.support');
	}
	public function use_agreement()
	{
		//return view('pages.use_agreement');
		return view('pages.use_agreement')->with('use_agreement', Application::use_agreement());
		
	}
}