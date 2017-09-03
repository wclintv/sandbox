<?php
namespace App\Models;

use Schema;
use App\Models\CustomerData;
use DB;

class CustomerDataList
{
	//Properties
	public $CustomerDataList = array();
	public $officeidlist = array();
	public $recordCount;
	
	//Methods
	public function Get()//Gets the entire BC CustomerDataList
	{ 
		if ($data = self::GetCustomerDataList($this->officeidlist))//returns cust_id field only for every cust_id in the database 
		{ 
			self::LoadData($data);
		}
	}
	public function GetUpdates($sync_timestamp) //Gets BC CustomerData that has been changed since the last sync
	{
		if ($data = self::GetCustomerDataListUpdates($this->officeidlist,$sync_timestamp))//returns cust_id field only for every cust_id in the database
		{
			self::LoadData($data);
		}
	}
	public function Get_Pagination($page)//Gets the BC CustomerDataList in sets of 30 records
	{ 
		if ($data = self::GetCustomerDataList_Pagination($this->officeidlist,$page))//returns cust_id field only for every cust_id in the database 
		{ 
			self::LoadData($data);
		}
	}
	public function GetUpdates_Pagination($page, $sync_timestamp) //Gets the BC CustomerDataList in sets of 30 records
	{
		if ($data = self::GetCustomerDataListUpdates_Pagination($this->officeidlist,$page,$sync_timestamp))//returns cust_id field only for every cust_id in the database 
		{ 
			self::LoadData($data);
		}
	}
	public function Update()
	{
		$countGet = 0; $countUpdate = 0;
		
		foreach ($this->CustomerDataList as $obj){
			$countGet = $countGet + 1;
			$customerdata = new CustomerData;
			$customerdata->DeserializeObject($obj);
			if($customerdata->Update() != 0){
				$countUpdate = $countUpdate + 1;
			}
		}
		$mesg1 = "Number of records in the array:" . $countGet . PHP_EOL;
		$mesg2 = "Number of CustomerData records updated:" . $countUpdate . PHP_EOL;
		return $mesg1 . $mesg2;
		
	}
	public function Create()//Don't send Create more than one Appointment or ServiceQuote!!!
	{
		$countGet = 0; $countCreate = 0;
		
		foreach ($this->CustomerDataList as $obj){
			$countGet = $countGet + 1;
			$customerdata = new CustomerData;
			$customerdata->DeserializeObject($obj);
			if($customerdata->Create() != 0){$countCreate = $countCreate + 1;}
		}
		$mesg1 = "Number of records in the array:" . $countGet . PHP_EOL;
		$mesg2 = "Number of records written to ALL FOUR Primary tables:" . $countCreate . PHP_EOL;
		return $mesg1 . $mesg2;
	}
	public function Sync()
	{
		$countGet = 0; $countUpdate = 0; $countCreate = 0;
		
		foreach ($this->CustomerDataList as $obj){
			$countGet = $countGet + 1;
			$customerdata = new CustomerData;
			$customerdata->DeserializeObject($obj);
			$action = $customerdata->Sync();
			if( $action == "update"){
				$countUpdate = $countUpdate + 1;
			}else if(is_int($action)){
				$countCreate = $countCreate + 1;
			}
		}
		$mesg1 = "Number of records in the CustomerDataList:" . $countGet . "\r\n";
		$mesg2 = "Number of records updated:" . $countUpdate . "\r\n";
		$mesg3 = "Number of records created:" . $countCreate . "\r\n";
		
		echo $mesg1 . $mesg2 . $mesg3;
		return $mesg1 . $mesg2 . $mesg3;
	}
	public function EchoJson(){return json_encode($this);}
	public function EchoJson_Pretty(){return json_encode($this, JSON_PRETTY_PRINT);}
	public function DeserializeJson($json)
	{
		$obj = json_decode($json);
		self::DeserializeObject($obj);
	}
	public function DeserializeObject($obj)
	{
		foreach ($obj as $name => $value){
			//'CustomerDataList' is referring to the object as a whole, not the properties of that object
			//if(property_exists('CustomerDataList', $name)){ //I don't understand why this isn't working now
				$this->$name = $value;
			//} 
		}
	}
	
	private function LoadData($data)
	{
		foreach($data as $value)
			{
				$custData = CustomerData::find($value->cust_id);
				$this->CustomerDataList[] = $custData;
				$this->recordCount = $this->recordCount + 1;
				unset($custData, $value);//"destroys" object to help with memory limit
			}
	}
	/*Database MySQL Calls*/
	private function GetCustomerDataList($officeidlist)//gets SINGLE Record from the Customer & Address tables
	{ 
	 	//Construct SQL Query
		return DB::select( 
			"
				SELECT cust_id
					FROM customer
					INNER JOIN appointment ON cust_id = aptcust_id
					INNER JOIN address ON aptaddress_id = address_id
					WHERE adroffice_id=" . implode(' OR adroffice_id=',$officeidlist) . "
					ORDER BY cust_id;
			");			
	}
	private function GetCustomerDataListUpdates($officeidlist,$timestamp)
	{ 
	 	//Construct SQL Query
		
		$query = 
			"
				SELECT cust_id
					FROM customer
					INNER JOIN appointment ON cust_id = aptcust_id
					INNER JOIN address ON aptaddress_id = address_id
					WHERE (adroffice_id=" . implode(' OR adroffice_id=',$officeidlist) . ")
					AND update_timestamp >= '" . $timestamp . "'
					ORDER BY cust_id;	
			";	
		return DB::select($query);	
	}
	private function GetCustomerDataList_Pagination($officeidlist,$start)
	{ 
	 	//Construct SQL Query
		return DB::select(
			"
				SELECT cust_id
					FROM customer
					INNER JOIN appointment ON cust_id = aptcust_id
					INNER JOIN address ON aptaddress_id = address_id
					WHERE adroffice_id=" . implode(' OR adroffice_id=',$officeidlist) . "
					ORDER BY cust_id
					LIMIT " . $start . ",30;
			");		
	}
	private function GetCustomerDataListUpdates_Pagination($officeidlist,$start,$timestamp)
	{ 
	 	//Construct SQL Query
		return DB::select(
			"
				SELECT cust_id
					FROM customer
					INNER JOIN appointment ON cust_id = aptcust_id
					INNER JOIN address ON aptaddress_id = address_id
					WHERE (adroffice_id=" . implode(' OR adroffice_id=',$officeidlist) . ")
					AND update_timestamp >= '" . $timestamp . "'
					ORDER BY cust_id
					LIMIT " . $start . ",30;
			");				
	}
}
?>