<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeStatus extends Model
{
    protected $table = 'employeestatus';
    protected $primaryKey = 'employeestatus_id';
    public function EchoJson(){return json_encode($this);}
}
