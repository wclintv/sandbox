<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Plugins\QuickbooksOnline\QuickbooksOnline;

class Office extends Model
{
    protected $table = 'office';
    protected $primaryKey = 'office_id';
	protected $fillable = 
    [
		'backup_timestamp',	
	    'oauth_token',
	    'oauth_token_created',	    
	    'oauth_token_secret',
		'officeaddress1',
		'officeaddress2',
		'officecity',	 
		'officename',
		'officestate_id',
		'officezipcode',
		'offprischedule',	
		'qb_admin_user_id',	
		'qb_realmid',
		'qb_setup_complete',
		'sync_timestamp',		
		'update_timestamp',
	];	

    //Methods
    public static function backup_timestamp_get($office_id)
    {
        return Office::where('office_id', $office_id)->first()->pluck('backup_timestamp')[0];
    }
    public static function by_realmid($realmid)
    {
        $result = null;
        $offices = Office::all();
        foreach($offices as $o)
        {
            if($o->qb_realmid == $realmid)
            {
                $result = $o;
            }
        }
        return $result;
    }
    public function EchoJson()
    {
    	return json_encode($this);
    }
    public function fetch_name()
    {
        $response = QuickbooksOnline::get("SELECT * FROM COMPANY", $this);
        $this->officename = $response['QueryResponse']['Company'][0]['CompanyName'];
        $this->save();
    }
    public function oauth_expired()
    {
        $oauth_token_created = $this->oauth_token_created;
        $expiration_date = date('Y-m-d h:i:s', strtotime('+180 days', strtotime($oauth_token_created)));
        $reconnect_date = date('Y-m-d h:i:s', strtotime('+152 days', strtotime($oauth_token_created)));

        $dt_create = new DateTime($oauth_token_created);
        $dt_expiration = new DateTime($expiration_date);
        $dt_reconnect = new DateTime($reconnect_date);
        $dt_now = new DateTime();

        if($dt_now >= $dt_reconnect)
        {
            return true;
        }
        return false;
    }
    public function oauth_update($access_token)
    {
        $this->oauth_token = $access_token['oauth_token'];
        $this->oauth_token_secret = $access_token['oauth_token_secret'];
        $this->oauth_token_created = date('Y-m-d H:i:s');   
        $this->qb_admin_user_id = $access_token['admin_id'];
        $this->qb_realmid = $access_token['realmid'];
        $this->save();
    }
	public static function office_id_get($realmid)
    {
        $o = Self::by_realmid($realmid);
        return $o->office_id;
    	//return Office::where('qb_realmid',$realmid)->first()->value('office_id');
    }
    public function reset_oauth($oauth_token = null, $oauth_secret = null)
    {
        $this->oauth_token = $oauth_token;
        $this->oauth_token_secret = $oauth_secret;
        $this->oauth_token_created = null;  
        if($oauth_token != null)
        {
            $this->oauth_token_created = date('Y-m-d H:i:s');  
        }        
        $this->save();
    }
	public static function sync_timestamp_get($office_id)
    {
        /*	
            capture the timestamp from the office as a datetime object.
    		$dt = new DateTime(Office::find($office_id)->value('sync_timestamp'));
    		$dt->modify("+1 second");
    		return $dt;
    		
        	return Office::select(DB::raw(
    			"
    			SELECT TIMESTAMPADD(SECOND,1,(
    				SELECT sync_timestamp FROM office WHERE office_id=" . $office_id ."
    			)) AS stamp;
    			"
    		));
        */
		
		
		return DB::selectOne(
			"
			SELECT TIMESTAMPADD(SECOND,1,(
				SELECT sync_timestamp FROM office WHERE office_id=?
			)) AS stamp;
			",[$office_id]
		);		
    }
	public static function sync_timestampQB_get($office_id)
    {
        /*        
    		-Add the letter "T" inbetween date and time to match QBs format

    		-Add one second to returned timestamp to allow for records that
             were part of the previous sync and updated their timestamps the
             same second as the BC timestamp was updated

    		-The CONVERT_TZ sql is a fix for the fact that we are using a GoDaddy
             shared hosting server that is set to MST time zone and QBs is set to PST
    		
            NOTE: We only neet to change CONVERT_TZ from '-07:00','-07:00' to '-07:00',
                  '-08:00' for half the year since AZ does not use daylight savings time
        */

    	return DB::selectOne(
			"
			SELECT DATE_FORMAT((
				SELECT TIMESTAMPADD(SECOND,45,(
					SELECT CONVERT_TZ((
						SELECT sync_timestamp FROM office WHERE office_id=?
					),'-07:00','-07:00')
				))
			), '%Y-%m-%dT%H:%i:%s') AS stamp ;
			",[$office_id]
		);
    }
    public static function sync_timestamp_update($office_id)
    {
		DB::unprepared(DB::raw(
				"UPDATE office SET sync_timestamp = CURRENT_TIMESTAMP WHERE office_id =" . $office_id .";"
		));
    }
    public static function update_timestamp_get($office_id)
    {
        return Office::where('office_id', $office_id)->first()->value('updated_at')[0];
    }

    //Relationships
    public function users()
    {
        $memberships = QboMembership::where('office_id', $this->office_id)->get();
        $users = [];
        foreach($memberships as $m)
        {
            $users[] = User::find($m->user_id);
        }
        return $users;
    }
    public function invitations()
    {
        return $this->hasMany('App\Models\Invitation','office_id','office_id');
    }
    public function memberships()
    {
        return $this->hasMany('App\Models\QboMembership','office_id','office_id');
    }

    //Mutators & Accessors
    public function getOauthTokenAttribute($value)
    {
        if($value != null)
        {
            return decrypt($value);
        }
        return $value;
    }    
    public function setOauthTokenAttribute($value)
    {
        $this->attributes['oauth_token'] = encrypt($value);
    }
    public function getOauthTokenSecretAttribute($value)
    {
        if($value != null)
        {
            return decrypt($value);
        }
        return $value;
    }
    public function setOauthTokenSecretAttribute($value)
    {
        $this->attributes['oauth_token_secret'] = encrypt($value);
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


    //Discarded Code
    /* 
        I don't think we need this, we'll see : P
        public static function update_timestamp_update($office_id)
        {
            $office = Office::where('office_id',$office_id)->first();
            $office->update_timestamp = date('Y:m:d H:m:s');
            $office->save();            
        }
        public function GetUpdateTimeStamp($office_id)
        {
            return DB::select('SELECT update_timestamp FROM office WHERE office_id= ?', [$office_id]);
        }
        public function GetSyncTimeStamp($office_id){
            //Add one second to returned timestamp to allow for records that were part of the previous sync and updated their timestamps the same second as the BC timestamp was updated
            return DB::select('SELECT TIMESTAMPADD(SECOND,1,(SELECT sync_timestamp FROM office WHERE office_id=' . $office_id .'));');
        }
        public function GetQBSyncTimeStamp($office_id)
        {
            //Add the letter "T" inbetween date and time to match QBs format
            //Add one second to returned timestamp to allow for records that were part of the previous sync and updated their timestamps the same second as the BC timestamp was updated
            //The CONVERT_TZ sql is a fix for the fact that we are using a GoDaddy shared hosting server that is set to MST time zone and QBs is set to PST
            //NOTE: We only neet to change CONVERT_TZ from '-07:00','-07:00' to '-07:00','-08:00' for half the year since AZ does not use daylight savings time
            return DB::select("SELECT DATE_FORMAT((SELECT TIMESTAMPADD(SECOND,1,(SELECT CONVERT_TZ((SELECT sync_timestamp FROM office WHERE office_id=" . $office_id ."),'-07:00','-07:00')))),'%Y-%m-%dT%H:%i:%s');");
        }
    */    
}


