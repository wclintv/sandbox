<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Invitation extends Model
{
    protected $table = 'invitations';
    protected $primaryKey = 'invitation_id';
    protected $fillable = 
    [
    	'email',
    	'firstname',
    	'lastname',
        'office_id',   	
    ];

    //methods
    public function expires()
    {
        $updated_at = $this->updated_at;
        $expires = $updated_at->copy()->addHours(48);
    	return $expires->format('d-M-Y h:i A');
    }
    public function expired()
    {
        $result = true;
        $updated_at = $this->updated_at;
        $expires = $updated_at->copy()->addHours(48);
        $now = Carbon::now();

        //if the invitation is expired, return false.
        if($now >= $expires)
        {
            $result = true;
        }
        else
        {
            $result = false;
        }
        return $result;
    }
    public static function by_email_by_office($email, Office $office)
    {
        $result = null;
        $invitations = $office->invitations;
        foreach($invitations as $i)
        {
            if($i->email == $email)
            {
                $result = $i;
            }
        }
        return $result;
    }
    public static function by_token($token)
    {
        $result = null;
        $i = Invitation::where('token', $token)->first();
        if($i != null)
        {
            $result = $i;
        }
        return $result;
    }


    //Assessors & Mutators
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

}
