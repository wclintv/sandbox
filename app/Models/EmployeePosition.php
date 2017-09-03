<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePosition extends Model
{
    protected $table = 'employeeposition';
    protected $primaryKey = 'employeeposition_id';

    public function EchoJson()
    {
    	return json_encode($this);
    }
}
