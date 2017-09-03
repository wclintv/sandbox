<?php
namespace App\Plugins\QuickbooksOnline\Models;

/*12*/
class QBDefaultTaxCodeRef
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