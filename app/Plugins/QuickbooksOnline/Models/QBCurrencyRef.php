<?php
namespace App\Plugins\QuickbooksOnline\Models;

/*4*/
class QBCurrencyRef
{
	//Properties
	public $value;
    public $name;

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