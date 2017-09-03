<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Housecode extends Model
{
    protected $table = 'housecode';
    protected $primaryKey = 'lettergrade_id';
    protected $fillable = [
    	'lettergrade_id',
    	'twostaffmin',
    	'threestaffmin',
    	'displayorder',
    ];
}
