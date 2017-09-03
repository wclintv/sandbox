<?php
namespace App\Plugins\QuickbooksOnline\Models;

/*5*/
class QBMetaData
{
	//Properties
	public $CreateTime;
    public $LastUpdatedTime;

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