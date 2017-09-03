<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
	public static function all()
    {
    	return PaymentMethod::all();
    }
    public static function find($paymentmethod_id)
    {
    	return PaymentMethod::find($paymentmethod_id);
    }
    
}
