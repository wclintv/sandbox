<?php
Route::get('login', 'LoginController@showLoginForm')->name('login');
Auth::routes();  //Laravel created login routes
	
//Public Routes
Route::get('/', 'PagesController@index');
Route::get('about', 'PagesController@about_get');
Route::get('application/icon', 'ApplicationController@icon');
Route::get('contact','PagesController@contact_get');
Route::get('file/image/{filename}','FileController@image');
Route::get('privacy_policy', 'PagesController@privacy_policy');
Route::get('qbo/media/{filename}', 'QuickbooksController@media');
Route::get('mediaplayer/video/{filename}','MediaPlayerController@video');
Route::get('requirements', 'PagesController@requirements_index');
Route::get('requirements/marketing', 'PagesController@requirements_marketing');
Route::get('requirements/security', 'PagesController@requirements_security');
Route::get('requirements/technical', 'PagesController@requirements_technical');
Route::get('support', 'PagesController@support');
Route::get('terms_of_service', 'PagesController@terms_of_service');
Route::get('use_agreement', 'PagesController@use_agreement');

	
//Secure Routes
Route::post('quickbooks/webhooks', 'QuickbooksController@webhooks')->middleware('webhooks');
Route::get('quickbooks/oauth_callback', 'QuickbooksController@oauth_callback')->middleware('sso');
Route::get('sso/{provider}/{method}', 'SsoController@route_to_provider')->middleware('sso');
Route::get('customers/datatable', 'CustomerController@datatable')->middleware('auth');
Route::get('customers/search', 'CustomerController@search')->middleware('auth');
Route::get('invitation/{encrypted_token}', 'InvitationsController@show')->middleware('invitation');
Route::middleware('auth')->group(function(){

	//Debugging Routes
	Route::middleware('debug_routes')->group(function(){
		Route::get('application','ApplicationController@show');
		Route::post('application','ApplicationController@update');
		Route::get('test1/{option}', 'TestController@test1');
		Route::get('test2', 'TestController@test2');
		Route::get('test3', 'TestController@test3');		
		Route::get('test4', 'TestController@test4');
		Route::get('login_test', 'TestController@login_test');
		Route::get('mail_test', 'TestController@mail_test');
		Route::get('pagination_test', 'TestController@pagination_test');	
		Route::get('test_qbo', 'TestController@test_qbo')	;
		Route::get('timestamps_test','TestController@time_stamps_test');
		Route::get('webhooks_test', 'TestController@webhooks_test');
	});

	//Standard Routes
	Route::get('apiexplorer', 'ApiController@client');
	Route::post('apiexplorer', 'ApiController@post');
	Route::get('api/states', 'StateController@all');	
	Route::post('api/application', 'ApplicationController@update');	
	Route::get('intuit_logout', 'UsersController@intuit_logout');
	Route::get('quickbooks', 'QuickbooksController@redirect');	
	Route::get('quickbooks/connect', 'QuickbooksController@connect')->middleware('quickbooks_connection_false');
	Route::get('users/profile', 'UsersController@show');
	Route::post('users/accept_terms', 'UsersController@accept_terms');
	Route::get('users/delete', 'UsersController@delete_current_user');	

	//Qbo required routes
	Route::middleware(['quickbooks_connection','setup'])->group(function (){
		//CustomerController
		Route::get('api/customers/{cust_id}', 'CustomerController@find');
		Route::put('api/customers/{cust_id}', 'CustomerController@update');
		Route::put('api/customers/validate', 'CustomerController@validate_request');
		Route::post('api/customers/{customer}', 'CustomerController@store');
		Route::get('customers', 'CustomerController@index');
		Route::get('customers/create', 'CustomerController@create');
		Route::get('customers/{cust_id}', 'CustomerController@show');
		Route::get('customers/{cust_id}/edit', 'CustomerController@edit');
	});

	//Qbo user admin required routes
	Route::middleware('quickbooks_admin')->group(function(){		
		Route::post('api/users/delete', 'UsersController@delete');		
		Route::post('api/invitation/delete', 'InvitationsController@delete');
		Route::post('api/invitation/resend', 'InvitationsController@resend');
		Route::post('api/invitation/send', 'InvitationsController@send');
		Route::put('api/invitation/validate', 'InvitationsController@invitation_validate');		
		Route::get('quickbooks/disconnect', 'QuickbooksController@disconnect');
		Route::get('quickbooks/oauth_start', 'QuickbooksController@oauth_start');
		Route::get('quickbooks/reconnect', 'QuickbooksController@reconnect');
		Route::get('settings', 'PagesController@settings');
		Route::get('users', 'UsersController@index');
	});
	
	Route::middleware('setup_false')->group(function()
	{
		Route::get('users/setup', 'UsersController@setup');
		Route::get('users/setup_sync', 'UsersController@setup_sync');
	});	


	Route::middleware('dev')->group(function()
	{
		
		Route::get('phpinfo', function(){
			return phpinfo();
		});
	});
	
});

Route::get('pathfinder/income', 'PathfinderController@income');
// Authentication Routes...
// Route::get('login', 'Auth\AuthController@login')->name('login');
// Route::post('login', 'Auth\AuthController@login');
// Route::post('logout', 'Auth\AuthController@logout')->name('logout');

// // Registration Routes...
// Route::get('register', 'Auth\AuthController@showRegistrationForm')->name('register');
// Route::post('register', 'Auth\AuthController@register');

// // Password Reset Routes...
// Route::get('password/reset', 'Auth\AuthController@showLinkRequestForm')->name('password.request');
// Route::post('password/email', 'Auth\AuthController@sendResetLinkEmail');
// Route::get('password/reset/{token}', 'Auth\AuthController@showResetForm');
// Route::post('password/reset', 'Auth\AuthController@reset');



