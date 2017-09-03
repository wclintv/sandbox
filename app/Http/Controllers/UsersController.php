<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\Invite;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Office;
use App\Models\QboMembership;
use Auth;
use Mail;
use App\Models\CustomerData;
use App\Models\CustomerSearchData;
use App\Utility\QLog;


class UsersController extends Controller
{
    //Web Handling
    public function accept_privacy_policy()
    {
        return view('admin.users.accept_privacy_policy');
    }
    public function accept_terms()
    {
        Auth::user()->terms_accepted = date("Y-m-d H:i:s");
        Auth::user()->save();
        return Auth::user();
    }
	public function create()
	{
		return view('admin.users.create');
	}
    public function delete_current_user()
    {
        $user = Auth::user();

        Auth::logout();

        if(User::delete_user($user))
        {
            return redirect('/');
        }
        return false;        
    }
    public function delete(Request $request)
    {
        $user = User::find($request->user_id);
        if($user != null)
        {
            return response((string)User::delete_user($user),200);
        }
        return (string) false;
    }
    public function index()
    {
        $office = Auth::user()->qbo_membership->office;
		return view('admin.users.index')->with('users', $office->users())->with('invitations', $office->invitations);
    }
    public function intuit_logout()
    {
        QLog::output("intuit_logout ");
        Auth::logout();
        return redirect('/');
    }

   
    public function setup()
    {
        return view('admin.users.setup')->with('popup_destination', '/customers');
    }
    public function setup_sync()
    {
        if(QuickbooksController::customer_load())
        {
            $office = Auth::user()->qbo_membership->office;
            $office->qb_setup_complete = true;
            $office->save();
            return 1;
        }
        return 0;
    }
    public function show()
    {
        return view('admin.users.show');
    }

    public function store(Request $request)
    {
    	User::create($request->all());
    	return 'user created';
    }
    public function update(Request $request)
    {
    	User::update($request->all());
    	return 'user updated';
    }
	public function destroy(Request $request)
	{
		//User::destroy($request->); //Where is this request object defined??
	}
}
