<?php
namespace App\Plugins\QuickbooksOnline\Models;

/*3*/
class QBParentRef
{
	//Properties
	public $value;
		
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