<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Utility\QLog;
use App\Models\User;
use App\Models\Office;

class QboMembership extends Model
{
    protected $table = 'qbo_membership';
    protected $primaryKey = 'qbo_membership_id';
    protected $fillable = 
    [
    	'qbo_membership_id',
    	'user_id',
    	'office_id',
    ];


    //Methods
    public static function by_user_by_office(User $user, Office $office)
    {
    	$result = null;
    	$m = QboMembership::where('user_id', $user->user_id)->where('office_id', $office->office_id)->first();
    	if($m != null)
    	{
    		$result = $m;
    	}
    	return $result;
    }
    public static function by_user_id_by_office_id($user_id, $office_id)
    {
        $result = null;
        $m = QboMembership::where('user_id', $user_id)->where('office_id', $office_id)->first();
        if($m != null)
        {
            $result = $m;
        }
        return $result;
    }
    public static function construct(User $user, Office $office)
    {
        $m = new QboMembership;
        $m->user_id = $user->user_id;
        $m->office_id = $office->office_id;
        $m->save();        
    }
    public static function delete_admin_membership(User $user)
    {
        /*
            here we delete all memberships related to a specific admin user and office
        */


        $result = false;
        if($user != null)
        {
            if($user->qb_is_admin == true)
            {
                //delete all the memberships for the given office;
                $memberships = $user->qbo_membership->office->memberships;
                if($memberships != null)
                {
                    foreach($memberships as $m)
                    {
                        QboMembership::destroy($m->qbo_membership_id);
                    }
                    $result = true;
                }             
            }            
        }
        return $result;
    }

    //Relationships
    public function office()
    {
    	return $this->hasOne('App\Models\Office','office_id','office_id');
    }
    public function user()
    {
    	return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }
}
