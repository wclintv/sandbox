<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table ='email';
    protected $primaryKey = 'email_id';
    protected $fillable = 
    [
        'from',
        'from_name',
        'message',            
        'sent_at',
        'subject',
        'to',
        'to_name',    
    ];
}
