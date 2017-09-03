<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use View;

class AppointmentController extends Controller
{
	public static function index()
	{
		$appointments = Appointment::paginate(10);
		return view('appointments.index')->with('appointments',$appointments);
	}
	public static function all()
	{
		return Appointment::all();
	}	
    public static function create(Array $data)
    {
		return Appointment::create($data);
	}
	public static function delete($appointment_id)
	{
		return "Function not built yet";
	}	
	public static function find($appointment_id)
	{
		return Appointment::with('customer')->find($appointment_id);
	}
	public static function update(Array $data)
	{		
		$appointment = Appointment::find($data['appointment_id']);
		$appointment->fill($data);
		$appointment->save();
		return Appointment::find($appointment->appointment_id);
	}
}
