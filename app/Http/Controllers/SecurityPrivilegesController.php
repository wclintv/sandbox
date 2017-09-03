<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SecurityPrivileges;

class SecurityPrivilegesController extends Controller
{
	public static function all()
    {
    	return SecurityPrivileges::all();
    }
    public static function find($securityprivileges_id)
    {
    	return SecurityPrivileges::find($securityprivileges_id);
    }
    
}