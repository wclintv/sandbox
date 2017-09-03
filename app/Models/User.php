<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\QboMembership;
use App\Models\CustomerData;
use App\Models\CustomerSearchData;
use App\Models\Invitation;
use Log;
use App\Utility\QLog;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
    'email',   
    'firstname',
    'lastname' ,
    'name',
    'openid_claimed_id',
    'openid_identity',
    'openid_sig',
    'openid_assoc_handle',
    'password',     
    'qb_is_admin',
    'qb_oauth_request_secret',
    'terms_accepted',   
    'userprivileges',
    ];
    protected $hidden = [
    'password', 'remember_token',
    ];

    //Methods
    public static function by_openid($openid_claimed_id)
    {
        $result = null;
        $u = User::where('openid_claimed_id',$openid_claimed_id)->first();
        if($u != null)
        {
            $result = $u;
        }
        return $result;
    }
    private static function delete_admin(User $user)
    {
        $result = false;
        $office = null;

        //Delete Memberships First
        if($user->qbo_membership != null)
        {
            $office = $user->qbo_membership->office;
            $users = $office->users();
            QboMembership::delete_admin_membership($user);
        }     


        //Delete Non-Admin Users
        foreach($users as $u)
        {
            if($u->qb_is_admin == false)
            {
                User::destroy($u->user_id);
            }            
        }

        if($office != null)
        {
            //delete all open invitations for the office
            foreach($office->invitations as $i)
            {
                Invitation::destroy($i->invitation_id);
            }

            //delete all CustomerData associated with the office 
            CustomerData::deleteall($office->office_id);
            CustomerSearchData::deleteall($office->office_id); 
            Office::destroy($office->office_id);            
        }

        if(User::destroy($user->user_id))
        {
            $result = true;
        }

        return $result;
    }
    public static function delete_user(User $user)
    {   
        $result = false;        

        if($user != null)
        {   
            //if the user is an qbo admin, redirect to special function             
            if($user->qb_is_admin == true)
            {
                return Self::delete_admin($user);
            }
            else
            {
                //if the user has a qbo membership, it must be destroyed before the user
                $membership = $user->qbo_membership;
                if($membership != null)
                {
                    QboMembership::destroy($membership->qbo_membership_id);
                }       

                User::destroy($user->user_id);
                $result = true;                
            }   
        }
        return $result;
    }


    //Relationships
    public function qbo_membership()
    {
        return $this->hasOne('App\Models\QboMembership', 'user_id', 'user_id');
    }


    //Accessors & Mutators
    public function getNameAttribute($value)
    {
        return $value;
    }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
    // public function getPasswordAttribute($value)
    // {
    //     return decrypt($value);
    // }
    public function getQbOauthRequestSecretAttribute($value)
    {
        if($value != null)
        {
            return decrypt($value);
        }
        return $value;
    } 
    public function setQbOauthRequestSecretAttribute($value)
    {
        $this->attributes['qb_oauth_request_secret'] = encrypt($value);
    }
    public function getQbRealmidAttribute($value)
    {
        if($value != null)
        {
            return decrypt($value);
        }
        return $value;
    }
    public function setQbRealmidAttribute($value)
    {
        $this->attributes['qb_realmid'] = encrypt($value);
    }   
    public function identity()
    {
        if($this->openid_claimed_id != null)
        {
            $url_array = explode('/', $this->openid_claimed_id);
            $identity = end($url_array);
            return $identity;
        }
        return false;
    }
}
