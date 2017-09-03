<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;

class OfficeController extends Controller
{
    public static function all()
    {
        return Office::all();
    }
    public static function create(Array $data)
    {
        return Office::create($data);
    }
    public static function delete($office_id)
    {
        return "Function not built yet";
    }
    public static function find($office_id)
    {
    	return Office::find($office_id);
    }
    public static function update(Array $data)
    {
        return Office::update($data);
    }
}