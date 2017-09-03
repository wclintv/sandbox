<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Email;
use App\Mail\Invite;
use App\Events\SendMail;
use Auth;
use Mail;
use Hash;

class InvitationsController extends Controller
{
    public function accept(Request $request)
    {
        
    }
    public function delete(Request $request)
    {
    	if(Invitation::destroy($request->invitation_id))
        {
            return 1;
        }
        return response('server error', 500);
    }

    public function send(Request $request)
    {   
        $data = $request->toArray();
        $admin = Auth::user();
        $office = $admin->qbo_membership->office;

        $user = null;
        //check if the user is already assigned to the qb company.
        foreach($office->users() as $u)
        {
            if($u->email == $data['email'])
            {
                $user = $u;
            }
        }

        //check if the user already exists
        //$user = User::by_email_by_realmid($data['email'], Auth::user()->qb_realmid);
        //$user = User::where('email', $data['email'])->where('qb_realmid', Auth::user()->qb_realmid)->first();

        if($user == null)
        {
            $invitation = new Invitation;
            $invitation->email = $data['email'];
            $invitation->firstname = $data['firstname'];
            $invitation->lastname = $data['lastname'];
            $invitation->office_id = $office->office_id;
            $invitation->token = $this->token_randomize();
            $invitation->save();

            //create a unique callback url...this will be decrypted by the callback function.
            $callback_url = url('/invitation/' . $invitation->token);
            
            $e = new Email;
            $e->to = $data['email'];
            $e->to_name = $data['firstname'] + " " + $data['lastname'];
            $e->from = $admin->email;
            $e->from_name = $admin->name;
            $e->sent_at = date('Y-m-d H:i:s'); 
            $e->subject = 'Invitation to SnapDsk.';
            $e->save();

            //send email invitation to potential user.
            if(Mail::to($e->to)->send(new Invite($e, $invitation, $callback_url)))
            {
                if(config('app.debug') == true)
                {
                   event(new SendMail($e));
                }             
            }
            return $data['email'];                
        }
        return response('That user is already setup.',422);
    }

    public function resend(Request $request)
    {
        $data = $request->toArray();
        $admin = Auth::user();
        $office = $admin->qbo_membership->office;
        
        //check if the user is already assigned to the qb company.
        $user = null;
        foreach($office->users() as $u)
        {
            if($u->email == $data['email'])
            {
                $user = $u;
            }
        }

        if($user == null)
        {
            //fetch the invitation by email, office_id
            $invitation = Invitation::by_email_by_office($data['email'], $office);
            //$invitation = Invitation::where('email', $data['email'])->where('qb_realmid', Auth::user()->qb_realmid)->first();

            if($invitation != null)
            {

                //update Invitation access token and updated_at fields
                $invitation->token = $this->token_randomize();
                $invitation->save();

                //create a unique callback url...this will be decrypted by the callback function.
                $callback_url = url('/invitation/' . $invitation->token);               


                $e = new Email;
                $e->to = $data['email'];
                $e->to_name = $data['firstname'] + " " + $data['lastname'];
                $e->from = Auth::user()->email;
                $e->from_name = Auth::user()->name;
                $e->sent_at = date('Y-m-d H:i:s'); 
                $e->subject = 'Invitation to SnapDsk.';
                $e->save();

	            //resend email invitation to potential user.
	           Mail::to($data['email'])->send(new Invite($e, $invitation, $callback_url));
               event(new SendMail($e));     

	           return $data['email'];        	
            }            
            return response('invitation not found.',403);            
        }
        return response('That user is already setup.',422);
    }
    public function show(Request $request, $encrypted_token)
    {
        return view('auth.invitation')->with('encrypted_token', $encrypted_token);
    }
    public function invitation_validate(Request $request)
    {
        if($request != null)
        {
            return $this->validate($request,
            [
                'firstname'     => 'required|max:35',
                'lastname'      => 'required|max:35',
                'email'         => 'required|email|unique:invitations'
            ],
            [
            ]);            
        }

        return null;
    }
    private function token_randomize()
    {
       $string = str_replace(' ', '-', Hash::make(str_random(32))); // Replaces all spaces with hyphens, so we can use it as part of a url.
       return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
