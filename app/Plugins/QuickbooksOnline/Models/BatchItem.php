<?php

namespace App\Plugins\QuickbooksOnline\Models;
use App\Models\CustomerData;

class BatchItem
{
	//Constructor
	function __construct()
	{
		$this->Customer = new QBCustomer;
	}

	//Properties
	public $bId;
	public $operation;	
	public $Customer = array();
	
	//Methods
	public static function create(QBCustomer $c)
	{
		$b = new BatchItem;
		$b->Customer = $c;
		//This will allow us to keep track of all three BC id's when we are matching the newly created QBs id's in the BatchItemResponse update();
		$b->bId = $custID . "&". $billAddrID . "&" . $shipAddrID; 
	}


	/*Database QuickBooks Calls*/
	private static function update(QBCustomer $c, Office $office)
	{
		//get response from quickbooks
		$array = QuickbooksOnline::batch(json_encode($this), $office);
		return $array;
	}	
}
?>