<?php
namespace App\Plugins\QuickbooksOnline\Models;

/*6*/
class QBPhone
{
	//Properties
	public $FreeFormNumber;

	public function fill(Array $array)
	{
		foreach($array as $key => $value)
		{
			$this->$key = $value;
		}
	}
}
?>