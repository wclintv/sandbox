<?php
namespace App\Plugins\QuickbooksOnline\Models;

use App\Models\CustomerData;

class QBBatchItem
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
	public static function from_qbCustomer(QBCustomer $c)
	{

	}


	/*Database QuickBooks Calls*/
	private static function update(QBCustomer $c, Office $office)
	{
		//set the url endpoint for REST call.
		//$url = env('URL_QBCOMPANY_BASE') . Auth::user()->qb_realmid . "/batch";
		//get response from quickbooks
		$array = QuickbooksOnline::batch(json_encode($this), $office);
		return $array;
	}	
}
?>