<?php
namespace App\Plugins\QuickbooksOnline\Models;

class QBWebAddress
{
	//Properties
	public $URI;
		
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