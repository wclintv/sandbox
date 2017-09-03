<?php
namespace App\Plugins\QuickbooksOnline\Models;

use App\Models\CustomerData;
use App\Models\CustomerDataList;
use App\Models\Address;
use App\Models\Appointment;
use App\Models\ServiceQuote;
use Log;

class BatchItemResponse
{
	//QuickBooks Properties
	public $BatchItemResponse = array();
	public $time;

	//Methods
	public function Update(){
		
		$recordCount['valid'] = 0; //this is the number of CustomerData records mapped from the QuickBooks QueryResponse or mapped to the BatchItemRequest 
		$recordCount['fault'] = 0;
		$num_rows = count($this->BatchItemResponse); //echo "the num_rows in BatchItemResponse is:" . $num_rows . "\r\n";
		$custDataList = new CustomerDataList; //It doesn't look like we really need this; I'll leave it for now for testing purposes
		
		/*NOTE: We need to write error handeling in here in case the BatchItemResponse returns "Fault" as the type instead of "Customer"*/
		
		/*NOTE: Becasue this is a BatchItemResponse, we are only updateing in BlueCard new information that is returned from QBs;
				we don't need to update the information that was just sent in the BatchItemRequest that initiated this response.*/
		
		/*This for loop is updateing BlueCard with the BatchItemResponse data*/
		for($x = 0; $x < $num_rows; $x++) {//Have to use this "for" loop instead of a "foreach" loop because QBs returns an Indexed Array as opposed to an Associative Array
		
			//Log::info($this->BatchItemResponse[Customer]);
					
			if (!empty($this->BatchItemResponse[$x]['Customer'])){ //BatchItemResponse can return other things such as "Fault" if one of the BatchItemRequest items failed
				
				//Log::info("In the Not Empty");
				
				/*Retrieve the three BlueCard IDs that were sent with the BatchItemRequest*/
				$ids = explode("&",$this->BatchItemResponse[$x]['bId']);//$custID & billAddrID & shipAddrID
				$custID = $ids[0]; $billAddrID = $ids[1]; $shipAddrID = $ids[2];
				//echo "custID " . $custID . "billAddrID " . $billAddrID . "shipAddrID " . $shipAddrID . "\r\n";
				
				/*BlueCard Customer Object*/
				$custDataRecord = new CustomerData; //create a new CustomerData object
				$custDataRecord->Customer->cust_id = $custID;
				$custDataRecord->Customer->qbid = $this->BatchItemResponse[$x]['Customer']['Id'];
				$custDataRecord->Customer->parentqbid = self::validateParentQBID($x); //This might exist on an update instead of a create???							
				$custDataRecord->Customer->balancedue = $this->BatchItemResponse[$x]['Customer']['Balance'];
				$custDataRecord->Customer->qbsynctoken = $this->BatchItemResponse[$x]['Customer']['SyncToken'];
				$custDataRecord->Customer->qbtimemodified = self::validateLastUpdatedTime($x); 
		
				/*BlueCard Address Object*/
				$addr = new Address; //create a new Address object
			
				//IF a shipping address is returned Update the matching BC adrressID and mark adrisbilling to "False"
				if (isset($this->BatchItemResponse[$x]['Customer']['ShipAddr']['Id']))
				{ 
					$addr->address_id = $shipAddrID;
					$addr->adrqbid = $this->BatchItemResponse[$x]['Customer']['ShipAddr']['Id'];
					
				//If a shipping address is not given BUT a billing address is then use the billing address as the shipping address	
				} elseif (isset($this->BatchItemResponse[$x]['Customer']['BillAddr']['Id']))
				{ 
					$addr->address_id = $billAddrID;
					$addr->adrqbid = $this->BatchItemResponse[$x]['Customer']['BillAddr']['Id'];
				}
				
				//NOTE:IF no address is given then populating these three attributes will create an address object to maintain the structural integrity of the CustomerData object;
				//However, nothing will be updated since the address_id was not populated and the CustomerData update() function check for address_id before allowing update	
				$addr->adrisbilling = 0; 
				$addr->adrqbsync = 1; //Only one Address record for any cust_id/address_id combination is allowed to be selected for qbsync at a time
				$addr->adrisactive = 1;
					
				$custDataRecord->AddressList[] = $addr; //load Address object into CustomerData object
				unset($addr); //"destroys" object to help with memory limit
				
				/*BlueCard Appointment Object*/
				//We don't need to update anything in the appoitment record from the BatchItemResponse and we're not persisting the appointmentID
				//this record is being created just as a placeholder to mantain the structural integrity of the CustomerData object, nothing should be updated
				$appt = new Appointment;//create a new Appointment object
				$appt->aptisactive = 0; //This will instantiate an appointment object to be added to the CustomerData object
				$custDataRecord->AppointmentList[] = $appt; //load Appointment object into CustomerData object
				unset($appt); //"destroys" object to help with memory limit
				
				/*BlueCard ServiceQuote Object*/
				//We don't need to update anything in the servicequote record from the BatchItemResponse and we're not persisting the servicequoteID
				//this record is being created just as a placeholder to mantain the structural integrity of the CustomerData object, nothing should be updated
				$srvqte = new ServiceQuote;//create a new ServiceQuote object
				$srvqte->qteisactive = 0; //This will instantiate a servicequote object to be added to the CustomerData object
				$custDataRecord->ServiceQuoteList[] = $srvqte; //load ServiceQuote object into CustomerData object
				unset($srvqte); //"destroys" object to help with memory limit
				
				CustomerData::update($custDataRecord->toArray()); //Send the newly mapped QBs ids to be updated in the BlueCard database
				
				$custDataList->CustomerDataList[] = $custDataRecord;
				$recordCount['valid'] = $recordCount['valid'] + 1;
				unset($custDataRecord); //"destroys" object to help with memory limit
			} else 
			{
				$recordCount['fault'] = $recordCount['fault'] + 1;
				/*NOTE: We need to write error handeling in here in case the BatchItemResponse returns "Fault" as the type instead of "Customer"*/
			}
		}
		return $recordCount;
	}	
	/*Validate Customer Object - when mapping QuickBooks BatchItemResponse to BlueCard*/
	private	function validateParentQBID($z){ 
		if (isset($this->BatchItemResponse->Customer[$z]['ParentRef']['value'])) {
			return $this->BatchItemResponse->Customer[$z]['ParentRef']['value'];
		} else { return null; }
	}
	private	function validateLastUpdatedTime($z)
	{ 
		if (isset($this->BatchItemResponse[$z]['Customer']['MetaData']['LastUpdatedTime']))
		{
			$dt = strtotime($this->BatchItemResponse[$z]['Customer']['MetaData']['LastUpdatedTime']);
			return date('Y-m-d H:i:s', $dt);
		} else { return null; }
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
		foreach ($obj as $name => $value)
		{
			$this->$name = $value;
		}
	}
}
?>