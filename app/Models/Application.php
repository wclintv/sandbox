<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table ='application';
    protected $primaryKey = 'application_id';
    protected $fillable = 
    [
        'address_id',
        'terms_of_service',
        'privacy_policy',
        'use_agreement',
    ];

    public static function privacy_policy()
    {
        $a = Application::first();
        return $a->privacy_policy;
    }
    public static function terms_of_service()
    {
    	$a = Application::first();
    	return $a->terms_of_service;
    }
    public static function use_agreement()
    {
        $a = Application::first();
        return $a->use_agreement;
    }
    public static function logo_120x120()
    {
        $path = storage_path('app/public/img/snapdsk_logo_default_120x120.png');
        return response()->file($path);
    }
}
