<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancelBy extends Model
{
    protected $table = 'cancelby';
    protected $primaryKey = 'cancelby_id';

    public function EchoJson()
	{
		return json_encode($this);
	}
}
