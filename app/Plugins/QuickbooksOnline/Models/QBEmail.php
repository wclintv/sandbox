<?php

namespace App\Plugins\QuickbooksOnline\Models;

class QBEmail
{
	//Properties
	public $Address;
		
	//Methods
	public function fill(Array $array)
	{
		foreach($array as $key => $value)
		{
			$this->$key = $value;
		}
	}
}
?>