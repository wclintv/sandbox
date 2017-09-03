<?php
namespace App\Plugins\QuickbooksOnline\Models;

use App\Plugins\QuickbooksOnline\QuickbooksOnline;
use App\Models\CustomerData;
use App\Models\CustomerDataList;
use App\Models\CustomerSearchData;
use App\Models\Customer;
use App\Models\Address;
use App\Models\Appointment;
use App\Models\ServiceQuote;
use App\Models\Office;
use App\Models\State;
use App\Models\Suffix;
use App\Models\PaymentMethod;
use App\Utility\QLog;

/*EVERYTHING IN THIS OBJECT IS QUICKBOOKS SPECIFIC*/
class QueryResponse
{ 
	//Constructor
	function __construct()
	{

	}
	//QuickBooks Properties
	public $QueryResponse = array();
	public $office;
	public $time;
	public $recordCount; //this is the number of CustomerData records mapped from the QuickBooks QueryResponse or mapped to the BatchItemRequest

	//Methods
	public function CustomerList_Get()//Where is this method being called from??
	{
		
		/*QuickBooks will only allow a maximum of 1000 records returned per query response so we will have to get all records through Pagination*/
		$startposition = 1;
		$json = null;
		$count = json_decode(self::GetCustomerCount());//Call QBs to find out how many total records are in the customer table
		$sets = ceil($count->QueryResponse->totalCount/1000);//Max sets of 1000
		
		for($x = 0; $x < $sets; $x++) {
			//Populate QueryResponse array
			if ($data = self::GetCustomerList($startposition)){
				if (empty($json)){
					$json = $data;
					
				}else{
					//this code is designed to merge two seperate json string together by converting them to arrays, merging the arrays, then converting them back to json strings
					$json = json_encode(array_merge_recursive(json_decode($data, true), json_decode($json, true)));
				}				
			}
			$startposition = $startposition + 1000; //this integer must match the intiger dividing total count and "MAXRESULTS" in the GetCustomerList() query
		}	
		self::DeserializeJson($json); /*Once ALL of the customer records are retrieved from QBs in sets, they are loaded into ONE BIG QueryResponse object for mapping with BC*/
	}	
	public function CustomerList_GetUpdates($sync_timestamp, Office $office)
	{

		/*QuickBooks will only allow a maximum of 1000 records returned per query response so we will have to get all records through Pagination*/
		$count = null;
		$startposition = 1;
		$json = null;
		$custList = null;

		//QLog::output('Made it into CustomerList_GetUpdates');
		
		//Call QBs to find out how many customer records have been updated since last sync
		$count = self::GetCustomerCountUpdates($office, $sync_timestamp);
		
		//QLog::output('WebHook QueryResp count:' . $count['QueryResponse']['totalCount']);
		QLog::output($count);

		if ($count['QueryResponse']['totalCount'] != 0)
		{		
			$sets = ceil($count['QueryResponse']['totalCount']/1000);//Max sets of 1000
			
			for($x = 0; $x < $sets; $x++)
			{
				//Populate QueryResponse array
				if ($data = self::GetCustomerListUpdates($office, $startposition, $sync_timestamp))//Update MAXRESULTS in GetCustomerListUpdates() to match pagination
				{
					//QLog::output($data);
					
					if (empty($custList) && ($sets > 1))
					{
						$custList = $data;
					}
					elseif (empty($custList) && ($sets <= 1))
					{
						$json = json_encode($data);
					}
					else
					{
						//this code is designed to merge two seperate arrays, then converting them to a json string
						$json = json_encode(array_merge_recursive($custList, $data));
					}				
				}
				$startposition = $startposition + 1000; //this integer must match the intiger dividing total count and "MAXRESULTS" in the GetCustomerList() query
			}	
			//Once ALL of the customer records are retrieved from QBs in sets, they are loaded into ONE BIG QueryResponse object for mapping with BC
			self::DeserializeJson($json); 
			
			return true;
		} else 
		{
			return false;
		}		
	}
	public function DeserializeJson($json)
	{
		$obj = json_decode($json);
		self::DeserializeObject($obj);
	}
	public function DeserializeObject($obj)
	{
		foreach ($obj as $name => $value)
		{
			//'QueryResponse' is referring to the object as a whole, not the properties of that object
		//	if(property_exists('QueryResponse', $name)) //I don't know why this isn't working now
		//	{
				$this->$name = $value;
		//	} 
		}
	}
	public function EchoJson()
	{
		return json_encode($this);
	}
	public function EchoJson_Pretty()
	{
		return json_encode($this, JSON_PRETTY_PRINT);
	}	
	public function Load()
	{
		$custUpdate = 0; $custCreate = 0;
		
		$custDataList = self::MapQBtoBC();
		
		foreach($custDataList->CustomerDataList as $key=>$custdata){ 
			
			/*This call updates or creates each record in BC that is recieved from QBs!!!*/			
			$actionCust = $custdata->Sync(); 

			if( $actionCust == "update"){
				$custUpdate = $custUpdate + 1;
			}else if($actionCust == "create"){
				$custCreate = $custCreate + 1;
			}
			unset($custdata);
		}	

		$mesg['count'] = $this->recordCount;
		$mesg['update'] = $custUpdate;
		$mesg['create'] = $custCreate;
		
		return $mesg;		
	}	
	private function MapQBtoBC($officeID) 
	{
		$this->recordCount = 0;
		$num_rows = count($this->QueryResponse->Customer);
		$custDataList = new CustomerDataList;
		
		/*NOTE: If a QBs child object does not contain a First & Last name then BC will not create a new customer record. */
		//Have to use this for loop instead of a foreach loop because QBs returns an Indexed Array as opposed to an Associative Array
		for($x = 0; $x < $num_rows; $x++) 
		{
			/*BlueCard Customer Object*/
			$custDataRecord = new CustomerData; //create a new CustomerData object
			$custDataRecord->Customer->cust_id = null; //not stored in QBs
			$custDataRecord->Customer->qbid = self::validateQBID($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->parentqbid = self::validateParentQBID($this->QueryResponse->Customer[$x]);
			
			$custDataRecord->Customer->title = self::validateTitle($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->firstname = self::validateGivenName($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->middlename = self::validateMiddleName($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->lastname = self::validateFamilyName($this->QueryResponse->Customer[$x]);
			//$custDataRecord->Customer->custsuffix_id = self::validateSuffixID($this->QueryResponse->Customer[$x]->Suffix);//This is the original BC custsuffix_id validation 
			$custDataRecord->Customer->suffix = self::validateSuffix($this->QueryResponse->Customer[$x]);
			
			$custDataRecord->Customer->phone = self::validatePrimaryPhone($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->altphone = self::validateAlternatePhone($this->QueryResponse->Customer[$x]); 
			$custDataRecord->Customer->mobilephone = self::validateMobilePhone($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->fax = self::validateFax($this->QueryResponse->Customer[$x]);
			
			$custDataRecord->Customer->company = self::validateCompany($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->email = self::validateEmail($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->website = self::validateWebSite($this->QueryResponse->Customer[$x]);
				
			//NOTE: when we move billing address into the address object we will need to set adrisbilling = 1;
			$custDataRecord->Customer->billingaddress1 = self::validateBillAddrLine1($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->billingaddress2 = self::validateBillAddrLine2($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->billingcity = self::validateBillAddrCity($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->billingstate_id = self::validateBillCountrySubDivisionCode($this->QueryResponse->Customer[$x]);
			$custDataRecord->Customer->billingzipcode = self::validateBillAddrPostalCode($this->QueryResponse->Customer[$x]);
			
			$custDataRecord->Customer->balancedue = self::validateBalance($this->QueryResponse->Customer[$x]->Balance);
			$custDataRecord->Customer->qbsynctoken = self::validateSyncToken($this->QueryResponse->Customer[$x]->SyncToken);
			$custDataRecord->Customer->qbstartdate = self::validateCreateTime($this->QueryResponse->Customer[$x]->MetaData->CreateTime);
			$custDataRecord->Customer->qbtimemodified = self::validateLastUpdatedTime($this->QueryResponse->Customer[$x]->MetaData->LastUpdatedTime);
			
			/*BlueCard Address Object*/
			$addr = new Address; //create a new Address object
			if (isset($this->QueryResponse->Customer[$x]->ShipAddr->Id)){
				$addr->address_id = null; //not stored in QBs
				$addr->adrqbid = self::validateShipAddrId($this->QueryResponse->Customer[$x]);
				$addr->adroffice_id = $officeID;
				$addr->address1 = self::validateShipAddrLine1($this->QueryResponse->Customer[$x]);
				$addr->address2 = self::validateShipAddrLine2($this->QueryResponse->Customer[$x]);
				$addr->city = self::validateShipAddrCity($this->QueryResponse->Customer[$x]);
				$addr->adrstate_id = self::validateShipCountrySubDivisionCode($this->QueryResponse->Customer[$x]);
				$addr->zipcode = self::validateShipAddrPostalCode($this->QueryResponse->Customer[$x]);
				$addr->adrisbilling = 0;
				$addr->adrqbsync = 1; //Only one Address record for any cust_id/address_id combination is allowed to be selected for qbsync at a time; this can only be assigned here in the QBtoBC sync
				$addr->adrisactive = 1;
			} else { //If a shipping address is not given use the billing address as the shipping address
				$addr->address_id = null; //not stored in QBs
				$addr->adrqbid = self::validateBillAddrId($this->QueryResponse->Customer[$x]);
				$addr->adroffice_id = $officeID; //self::validateOfficeId($x);
				$addr->address1 = self::validateBillAddrLine1($this->QueryResponse->Customer[$x]);
				$addr->address2 = self::validateBillAddrLine2($this->QueryResponse->Customer[$x]);
				$addr->city = self::validateBillAddrCity($this->QueryResponse->Customer[$x]);
				$addr->adrstate_id = self::validateBillCountrySubDivisionCode($this->QueryResponse->Customer[$x]);
				$addr->zipcode = self::validateBillAddrPostalCode($this->QueryResponse->Customer[$x]);
				$addr->adrisbilling = 0; 
				$addr->adrqbsync = 1; //Only one Address record for any cust_id/address_id combination is allowed to be selected for qbsync at a time; this can only be assigned here in the QBtoBC sync
				$addr->adrisactive = 1;
			}	
			$custDataRecord->AddressList[] = $addr; //load Address object into CustomerData object
			unset($addr); //"destroys" object to help with memory limit
			
			/*BlueCard Appointment Object*/
			$appt = new Appointment;//create a new Appointment object
			$appt->aptqbsync = 1; //Only one Appointment record for any cust_id/address_id combination is allowed to be selected for qbsync at a time; this can only be assigned here in the QBtoBC sync
			$appt->aptisactive = 1; //This will activate an Appointment object and cause it to be added to the CustomerData record
			$appt->aptpaymentmethod_id = self::validatePaymentMethod($this->QueryResponse->Customer[$x]);
			$custDataRecord->AppointmentList[] = $appt; //load Appointment object into CustomerData object
			unset($appt); //"destroys" object to help with memory limit
			
			/*BlueCard ServiceQuote Object*/
			$srvqte = new ServiceQuote;//create a new ServiceQuote object
			$srvqte->qtefreq_id = 8; //This field is required by BC and will activate a ServiceQuote object and cause it to be added to the CustomerData record
			$srvqte->qteservicetime_id = 5; //This field is required by BC
			$srvqte->notes = self::validateQBNotes($this->QueryResponse->Customer[$x]);
			$srvqte->qteqbsync = 1; //Only one of many ServiceQuotes associated with any one Appointment record is allowed to be selected for qbsync at a time; this can only be assigned here in the QBtoBC sync 
			$srvqte->qteisactive = 1;
			$custDataRecord->ServiceQuoteList[] = $srvqte; //load ServiceQuote object into CustomerData object
			unset($srvqte); //"destroys" object to help with memory limit
			
			$custDataList->CustomerDataList[] = $custDataRecord;
			$this->recordCount = $this->recordCount + 1;
			unset($custDataRecord); //"destroys" object to help with memory limit
		}
		return $custDataList;
	}
	public function SyncFromQB($officeID, $officeIdList, $sync_timestamp)//this function gets data from QBs and Updates existing records in the BC database if they exist, if the record does not exist in BC then it Creates it
	{ 
		$mesg['count'] = 0;
		$mesg['QBconflict'] = 0;
		$mesg['BCconflict'] = 0;
		$mesg['update'] = 0;
		$mesg['create'] = 0;

		$custDataList_QBConflict = new CustomerDataList;
		$custDataList_BCConflict = new CustomerDataList;
		$customersearchdata = new CustomerSearchData;
		
		/*NOTE: Not using this conflict checking code in V1*/
		/*Create a CustomerDataList and load it to find out how many updated records are in BC*/
		//The GetUpdates function retrieves the cutomer records from BC that have been updated since the last sync,
		//if any of those records match the CustomerDataList of updated records we recieved from QBs then there is a conflict
		//$custDataList_BCupdates = new CustomerDataList;
		//$custDataList_BCupdates->DeserializeJson($officeIdList);
		//$custDataList_BCupdates->GetUpdates($sync_timestamp);
		
		//QLog::output('Made it into SyncFromQB');
		
		$custUpdate = 0; $custCreate = 0;
		
		$custDataList_QBupdates = self::MapQBtoBC($officeID);
		
		foreach($custDataList_QBupdates->CustomerDataList as $key=>$custData_QBupdate)
		{ 

			//NOTE: when getting data from QBs it is possible that a new customer could be using an address that already exists in BC so we have to check those independantly
			//if you have an existing address AND an existing customer then you will have an appointment, otherwise a new appointment will have to be created
			
			//you can't have a cutomer without an address and you can't have a customer and an address without an appointment so you can't have a serivcequote without an appointment
			//it is possible for quickbooks to send an existing address with a new customer but their won't be an existing service quote for a new customer 
			
			/*This comment is interesting!!! Think more on this:*/
			//QBs will never send more than one serviceqote associated with a customer and each BC service quote will be stored in QBs as a seperate "child" customer object
			
			//QBs can send new customers with no address but with notes which means that we need a servicequote and in order to get a servicequote you must have an appointment 
			//and in order to get an appointment you must have a customer and an address
			//If QBs sends a customer record with no address associated then BC will create a default "unkown" address object in the validation so that an address record will be created. 

			
			/*Get the BlueCard custID, addressID, appointmentID, and servicequoteID for each record recievd from QuickBooks*/
			$custdata_QBupdate = self::GetBlueCardIDs($officeID, $custData_QBupdate);
			
			$trigger = 0;
			
			/*NOTE: For V1 we are going to default all conflicts to QuickBooks so we are not going to use the Check Update Conflicts code below.  
			        In V2 we need to create an async que to handle conflicts*/
	
			/*Check for Update Conflicts -- Verify that None of the QueryResponse records have been updated in BlueCard since the last QuickBooks update*/
			//NOTE: Any conflicting QBs records will be returned to user along with the conflicting BC record.
			//If the user keeps the QB version then send that QB CustomerDataList straight to the CustomerDataList_Update() function.
			//IF the user keeps the BC version then send that BC CustomerDataList straight to the Quickbooks_Update() function.
			
			/*
			if(!empty($custDataList_BCupdates->CustomerDataList))
			{	
				$num_rows = count($custDataList_BCupdates->CustomerDataList);
					
				for($x = 0; $x < $num_rows; $x++)
				{	
					//If the QuickBooks updated cust_id is equal to the BlueCard updated cust_id then there is a conflict
					if($custdata_QBupdate->Customer->cust_id == $custDataList_BCupdates->CustomerDataList[$x]->Customer->cust_id)
					{ 	
						//Update the BC customer record with the new synctoken here to facilitate updates to QBs if user wants to keep BC version of data
						$custDataList_BCupdates->CustomerDataList[$x]->Customer->qbsynctoken = $custdata_QBupdate->Customer->qbsynctoken;

						$custDataList_QBConflict->CustomerDataList[] = $custdata_QBupdate;//copy the conflicting QB CustomerData record to a list to be sent to the user
						$custDataList_BCConflict->CustomerDataList[] = $custDataList_BCupdates->CustomerDataList[$x];//copy the conflicting BC CustomerData record to a list to be sent to the user
						
						$trigger = 1;//if it finds a match don't Sync the CustomerData object
						
					//If the BlueCard record does not have a QBs ID then it is a NEW record and we must check to make sure it wasn't created in QBs		
					//If the Quickbooks record does not have a BlueCard ID then it is a NEW record and we much check to make sure it wasn't created in BC	
					} elseif ((empty($custDataList_BCupdates->CustomerDataList[$x]->Customer->qbid)) || (empty($custdata_QBupdate->Customer->cust_id)))
					{
						if(($custdata_QBupdate->Customer->firstname == $custDataList_BCupdates->CustomerDataList[$x]->Customer->firstname) &&
							($custdata_QBupdate->Customer->lastname == $custDataList_BCupdates->CustomerDataList[$x]->Customer->lastname))
						{
							//Update the BC customer record to display with the new synctoken to facilitate updates to QBs if user wants to keep BC version of data
							$custDataList_BCupdates->CustomerDataList[$x]->Customer->qbsynctoken = $custdata_QBupdate->Customer->qbsynctoken;

							//Update the BC customer record to display with the new QuickBooks ID to facilitate updates to QBs if user wants to keep BC version of data
							//NOTE: QuickBooks will not allow you to create two unique records with the same first and last name
							$custDataList_BCupdates->CustomerDataList[$x]->Customer->qbid = $custdata_QBupdate->Customer->qbid;

							//Update the QBs customer record to display with BC primary object IDS to faciliate updates to BC if user wants to keep the QBs version of data
							//NOTE: On CREATES there should not be more than one Address, Appointment, or ServiceQuote associated with any record; hence, the hard coded "0" on the arrays
							$custdata_QBupdate->Customer->cust_id = $custDataList_BCupdates->CustomerDataList[$x]->Customer->cust_id;
							$custdata_QBupdate->AddressList[0]->address_id = $custDataList_BCupdates->CustomerDataList[$x]->AddressList[0]->address_id;
							$custdata_QBupdate->AppointmentList[0]->appointment_id = $custDataList_BCupdates->CustomerDataList[$x]->AppointmentList[0]->appointment_id;
							$custdata_QBupdate->ServiceQuoteList[0]->servicequote_id = $custDataList_BCupdates->CustomerDataList[$x]->ServiceQuoteList[0]->servicequote_id;

							//copy the conflicting QB CustomerData record to a list to be sent to the user
							$custDataList_QBConflict->CustomerDataList[] = $custdata_QBupdate;
							//copy the conflicting BC CustomerData record to a list to be sent to the user
							$custDataList_BCConflict->CustomerDataList[] = $custDataList_BCupdates->CustomerDataList[$x];
							
							$trigger = 1;//if it finds a match don't Sync the CustomerData object							
						}
					}
				} 
			}
			*/
			if($trigger == 0)
			{	
				//the cust_id is unset during the Update portion of the Sync so we need to capture the cust_id BEFORE the Sync takes place
				//but we need to Get the updated CustomerSearchData AFTER the Sync has made its changes
				if(!empty($custdata_QBupdate->Customer->cust_id))
				{
					$custID = $custdata_QBupdate->Customer->cust_id;
				}
					
				/*This call updates or creates each record in BC that is recieved from QBs!!!*/
				$actionCust = $custdata_QBupdate->sync(); 
				
				//QLog::output('$actionCust is:' . $actionCust);
				
				if( $actionCust == "update")
				{
					$custUpdate = $custUpdate + 1;
				}
				elseif(is_int($actionCust->Customer->cust_id))//the CustomerData Sync function returns the newly created CustomerData record when the sync runs the create function
				{ 
					$custCreate = $custCreate + 1;
				}
			}
			
			unset($custdata_QBupdate); //this ensures that no CustomerData records are persisted through the next foreach loop iteration
		}
		
		$mesg['count'] = $this->recordCount;
		$mesg['QBconflict'] = $custDataList_QBConflict;
		$mesg['BCconflict'] = $custDataList_BCConflict;
		$mesg['update'] = $custUpdate;
		$mesg['create'] = $custCreate;
				
		return $mesg;
	}		



	/*Get BlueCard IDs associated with records recieved from QuickBooks*/
	private function GetBlueCardIDs($officeID, $custdata)
	{
		//Get CustomerID
		if(isset($custdata->Customer->qbid) && empty($custdata->Customer->cust_id))
		{
			//If the record exists AND cust_id had not been assigned then assign the retrieved cust_id
			if ($data = self::GetCustomerIDwithQBID($officeID, $custdata->Customer->qbid))
			{
				//when records are recieved from QBs they will not include the cust_id so assign it
				$custdata->Customer->cust_id = $data; 
			}
			unset($data);
		}
		
		//Get AddressID
		//AddressID has to be searched for seperate from CustomerID because it is possible for QBs to send a new customer with and existing address
		//currently this should only return one record but if we start saving billing address in the address table then it could be two
		//At most we can recieve 1 unique billing and 1 unique shipping address, 
		//If only a billing address is given it is copied into the shipping address by MayQBtoBC() function
		//Their should be no ServiceQuotes associated with billing address objects; We need to write code to make sure we keep that from happening
		foreach($custdata->AddressList as $addrs)
		{ 
			if(isset($custdata->Customer->cust_id))
			{ 
				//If their is no address record yet created in BC then this "if" statement will fail 
				if ($data = self::GetAddressIDwithBCID($custdata->Customer->cust_id))
				{	
					//when records are recieved from QBs they will not include the address_id so assign it
					$addrs->address_id = $data; 
				}
			} elseif(isset($addrs->adrqbid))//Sometimes an existing address will be sent with a new customer
			{	
				//If their is no address record yet created in BC then this "if" statment will fail
				if ($data = self::GetAddressIDwithQBID($officeID, $addrs->adrqbid))
				{
					//when records are recieved from QBs they will not include the address_id so assign it
					$addrs->address_id = $data; 
				} 	
			}
			unset($data);
		
		//REMOVE THIS CODE IS EVERYTHING IS WORKING PROPERLY
			if(isset($addrs->adrqbid))
			{	
				//If their is no address record yet created in BC then this "if" statment will fail
				if ($data = self::GetAddressIDwithQBID($officeID, $addrs->adrqbid))
				{
					//At most we can recieve 1 unique billing and 1 unique shipping address, 
					//If only a billing address is given it is copied into the shipping address by MayQBtoBC() function
					//Their should be no ServiceQuotes associated with billing address objects; We need to write code to make sure we keep that from happening
					
					//when records are recieved from QBs they will not include the address_id so assign it
					$addrs->address_id = $data; 
				} 
				
				//This Else is to handle a scenario in which an adrqbid exists but there is no matching address record in BC; this will occur when
				//an UPDATE is made to use the Billing addressID as the Shipping addressID i.e. when the QBs user checks the "Same As Billing" box for shipping in QBs
				//after the record has already been initally created.  
				
				//Since an Appointment record already exists linking the Customer and Address data then we are going to Update the existing
				//address record information, including adrqbid, and keep the already existing address_ID.
				
				else
				{
					//create a new record in Address table with $custdata billing address information
					Address::insert($addrs->toArray());
					//assign the newly created BC addressID to the $addrs object 
					$addrs->address_id = self::GetAddressIDwithQBID($officeID, $addrs->adrqbid);
					
					//NOTE: Test to make sure Appointment has the newly created Address ID from billing
					//Also, what happens when they switch back to a seperate shipping address
				}	
				unset($data);
				
			} elseif(isset($custdata->Customer->cust_id)) 
			{ 
				//We can't just use this else statment irregardless of the if part because sometimes an existing address will be sent with a new customer
			  //If their is no address record yet created in BC then this "if" statment will fail
				if ($data = self::GetAddressIDwithBCID($custdata->Customer->cust_id))
				{	
					//when records are recieved from QBs they will not include the address_id so assign it
					$addrs->address_id = $data; 
				}
				unset($data);
			}
			
			//Do Not match Billing addressID with the customerID for purposes of updating the ServiceQuote
			if(($addrs->adrisbilling == 0) && isset($addrs->address_id)) 
			{
				//If the addressID did not exist and the address should be created in BC when custdata->Sync is called below
				$addrID = $addrs->address_id;
			} 
		}	
		
		//Get AppoitmentID
		//BlueCard can have multiple appointments for any cust_id and address_id combination
		//However, QuickBooks will only have one appointment associated with any cust_id and address_id combination: aptQbSync = 1
		
		//NOTE: Only one aptqbsync and srvqbsync field in the database record can be set to "1" for any given customer and address combo
		//If I'm syncing from BlueCare to QuickBooks, I will know which set of notes from various ServiceQuotes to sync into QuickBooks because: srvQbSync = 1
		
		//Pull the appointment_id that is allowed to sync in order to get the associated ServiceQuote that is allowed to sync
		//If the customer record exists in BlueCard then an address and appoitment record must also exist and at least one aptQbSync will be set to "1"
		if (isset($custdata->Customer->cust_id) && isset($addrID))//this would be set in the code above
		{ 
			//This datbase call returns appointment_id based on cust_id, address_id, and aptqbsync = 1
			if ($data = self::GetAppointmentIDwithCustIDAddrID($custdata->Customer->cust_id,$addrID))
			{	
				foreach($custdata->AppointmentList as $appt)
				{
					//If there are multiple appointments only pull the one that is allowed to be synced with QBs
					//If the record exists AND appointment_id had not been assigned 
					if($appt->aptqbsync == 1 && empty($appt->appointment_id))
					{ 						
						//when records are recieved from QBs they will not include the appointment_id so assign it 
						$appt->appointment_id = $data; 
						$apptID = $appt->appointment_id;
					}
				}	
				unset($data);		
			}
		}
		
		//Get ServiceQuoteID
		//Pull the servicequote_id that is allowed to sync in order to update the record
		//If the customer record exists in BlueCard then an address and appointment record must also exist and at least one aptQbSync will be set to "1"
		//A ServiceQuote record will exist even if no notes are created in QBs because the validation method in MapQBtoBC will initiate it
		if (isset($apptID)) { //this would be set in the code above
			//This datbase call returns servicequote_id based on appointment_id and qteqbsync = 1
			
			if ($data = self::GetServiceQuoteIDwithApptID($apptID))
			{ 
				foreach($custdata->ServiceQuoteList as $srvqte)
				{
					//If there are multiple appointments only pull the one that is allowed to be synced with QBs
					//If the record exists AND servicequote_id had not been assigned 
					if($srvqte->qteqbsync == 1 && empty($srvqte->servicequote_id))
					{
						//when records are recieved from QBs they will not include the servicequote_id so assign it 
						$srvqte->servicequote_id = $data;
					}
				}
				unset($data);
			}
		}
		
		unset($addrID, $apptID);//this ensures that no Appointment or ServiceQuote are created using an old AddressID or AppointmentID

		return $custdata;
	}


	/*Database MySQL Calls*/
	private function GetCustomerIDwithQBID($officeID, $qbid)
	{ 
		return Customer::where('qbid',$qbid)
						->join('appointment', 'appointment.aptcust_id', '=', 'customer.cust_id')
						->join('address', 'address.address_id', '=', 'appointment.aptaddress_id')
						->where('adroffice_id', '=', $officeID)
						->value('cust_id');	
	}
	private function GetAddressIDwithQBID($officeID, $adrqbid)
	{
		return Address::where('adrqbid',$adrqbid)
						->where('adroffice_id', '=', $officeID)
						->value('address_id');
	}
	private function GetAddressIDwithBCID($custID)
	{
		//$query = " SELECT IFNULL((SELECT aptaddress_id FROM appointment WHERE aptcust_id='" . $custID . "' AND aptqbsync=1),0) AS 'result';";
		
		return Appointment::where('aptcust_id',$custID)
							->where('aptqbsync', '=', 1)
							->value('aptaddress_id');
	}
	private function GetAppointmentIDwithCustIDAddrID($custID,$addressID)
	{
		//$query = " SELECT IFNULL((SELECT appointment_id FROM appointment WHERE aptcust_id='" . $custID . "' 
		//AND aptaddress_id='" . $addressID . "' AND aptqbsync=1),0) AS 'result';";
		
		return Appointment::where('aptcust_id',$custID)
							->where('aptaddress_id',$addressID)
							->where('aptqbsync', '=', 1)
							->value('appointment_id');
	}
	private function GetServiceQuoteIDwithApptID($appointmentID)
	{
		//$query = " SELECT IFNULL((SELECT servicequote_id FROM servicequote WHERE qteappointment_id='" . $appointmentID . "' AND qteqbsync=1),0) AS 'result';";
	
		return ServiceQuote::where('qteappointment_id',$appointmentID)
							->where('qteqbsync', '=', 1)
							->value('servicequote_id');
	}
	
	/*Database QuickBooks Calls*/
	private function GetCustomerCount()
	{
		$query = "SELECT COUNT(*) FROM Customer";
		return QuickbooksOnline::get($query);//array returned	
	}
	private function GetCustomerList($z)
	{
		$query = "SELECT * FROM Customer STARTPOSITION " . $z . " MAXRESULTS 1000";
		return QuickbooksOnline::get($query);//array returned	
	}
	private function GetCustomerCountUpdates(Office $office, $timestamp)
	{
		$query = "SELECT COUNT(*) FROM Customer WHERE MetaData.LastUpdatedTime >= '" . $timestamp . "'";
		
		//QLog::output('The query in GetCustomerCountUpdates is: ' . $query);
		//QLog::output('The realmID in GetCustomerCountUpdates is: ' . $realmID);
		
		return QuickbooksOnline::get($query, $office);//array returned	
	}
	private function GetCustomerListUpdates(Office $office, $z, $timestamp)//Update MAXRESULTS to match pagination above
	{
		$query = "SELECT * FROM Customer WHERE MetaData.LastUpdatedTime >= '" . $timestamp . "' STARTPOSITION " . $z . " MAXRESULTS 1000";
		return QuickbooksOnline::get($query, $office);//array returned
	}


	
	/*Validate Customer Object - when mapping QuickBooks to BlueCard*/
	private	function validateQBID($qbid)
	{ 
		if (isset($qbid->Id)) 
		{
			return $qbid->Id;
		}
		return null;
	}
	private	function validateParentQBID($parentID)
	{ 
		if (isset($parentID->ParentRef->value)) {
			return $parentID->ParentRef->value;
		}
		return null;
	}	
	private	function validateTitle($title)
	{	
		if (isset($title->Title)) {
			return $title->Title;
		}
		return null;
	}
	private	function validateGivenName($givenName)
	{ 
		if (isset($givenName->GivenName)) 
		{
			return $givenName->GivenName;
		}
		return "unknown";
	}
	private	function validateMiddleName($middleName)
	{ 
		if (isset($middleName->MiddleName)) 
		{
			return $middleName->MiddleName;
		}
		return null;
	}
	private	function validateFamilyName($familyName)
	{ 
		if (isset($familyName->FamilyName)) 
		{
			return $familyName->FamilyName;
		}
		return "unknown";
	}
	private	function validateSuffixID($suffix)
	{ 
		if (isset($suffix)) 
		{
			
			//NOTE: need to add code to validate different variations of Suffix
			
			$suffix = new Suffix;
			$suffix->suffix = $suffix;
			$suffix->GetID();
			return $suffix->suffix_id;
		}
		return null;
	}
	private function validateSuffix($suffix)
	{
		if (isset($suffix->Suffix)) 
		{
			return $suffix->Suffix;
		} 
		return null;
	}
	private	function validateBillAddrId($addrID)
	{ 
		if (isset($addrID->BillAddr->Id)) 
		{
			return $addrID->BillAddr->Id;
		}
		return null;
	}
	private	function validateBillAddrLine1($address)
	{ 
		if (isset($address->BillAddr->Line1)) 
		{
			return $address->BillAddr->Line1;
		}
		return "unknown";//"unknown" is returned so that an address object will be created even if there is no address data given
	}
	private	function validateBillAddrLine2($address)
	{ 
		if (isset($address->BillAddr->Line2)) 
		{
			return $address->BillAddr->Line2;
		}
		return null;
	}
	private	function validateBillAddrCity($city)
	{ 
		if (isset($city->BillAddr->City)) 
		{
			return $city->BillAddr->City;
		} 
		return "unknown";//"unknown" is returned so that an address object will be created even if there is no address data given
	}
	private	function validateBillCountrySubDivisionCode($stateAbrv)
	{ 
		if (isset($stateAbrv->BillAddr->CountrySubDivisionCode)) 
		{
			//NOTE: need to add code to validate different variations of State abbreviation & select for State name as well		
			return State::where('stabrv',$stateAbrv->BillAddr->CountrySubDivisionCode)->value('state_id');
		} 
		return null; 
	}
	private	function validateBillAddrPostalCode($zipcode)
	{ 
		if (isset($zipcode->BillAddr->PostalCode)) 
		{
			return $zipcode->BillAddr->PostalCode;
		}
		return "unknown";//"unknown" is returned so that an address object will be created even if there is no address data given
	}
	private	function validatePrimaryPhone($phone)
	{
		if (isset($phone->PrimaryPhone->FreeFormNumber)) 
		{
			return $phone->PrimaryPhone->FreeFormNumber;
		} 
		/*elseif (isset($this->QueryResponse->Customer[$z]->Mobile->FreeFormNumber))
		{
			return $this->QueryResponse->Customer[$z]->Mobile->FreeFormNumber;
		} */
		return null; 
	}
	private	function validateAlternatePhone($phone)
	{
		if (isset($phone->AlternatePhone->FreeFormNumber)) 
		{
			return $phone->AlternatePhone->FreeFormNumber;
		} 
		return null; 
	}
	private	function validateMobilePhone($phone)
	{
		if (isset($phone->Mobile->FreeFormNumber)) 
		{
			return $phone->Mobile->FreeFormNumber;
		} 
		return null; 
	}
	private	function validateFax($phone)
	{
		if (isset($phone->Fax->FreeFormNumber)) 
		{
			return $phone->Fax->FreeFormNumber;
		} 
		return null; 
	}
	private function validateCompany($company)
	{
		if (isset($company->CompanyName)) 
		{
			return $company->CompanyName;
		} 
		return null;
	}
	private	function validateEmail($email)
	{ 
		if (isset($email->PrimaryEmailAddr->Address)) 
		{
			return $email->PrimaryEmailAddr->Address;
		}
		return null;
	}
	private	function validateWebSite($website)
	{
		if (isset($website->WebAddr->URI)) 
		{
			return $website->WebAddr->URI;
		}
		return null;
	}
	private	function validateBalance($balance)
	{ 
		if (isset($balance)) 
		{
			return $balance;
		}
		return null;
	}
	private	function validateSyncToken($syncToken)
	{ 
		if (isset($syncToken)) 
		{
			return $syncToken;
		}
		return null;
	}
	private	function validateCreateTime($createTime)
	{ 
		if (isset($time)) 
		{
			$dt = strtotime($time);
			return date('Y-m-d H:i:s', $dt);
		}
		return null;
	}
	private	function validateLastUpdatedTime($updateTime)
	{ 
		if (isset($updateTime)) 
		{
			$dt = strtotime($updateTime);
			return date('Y-m-d H:i:s', $dt);
		}
		return null;
	}
	
	/*Validate Address Object - when mapping QuickBooks to BlueCard*/
	private	function validateShipAddrId($addrID)
	{ 
		if (isset($addrID->ShipAddr->Id)) 
		{
			return $addrID->ShipAddr->Id;
		}
		return null;
	}
	private	function validateOfficeId($officeID)
	{ 
		return $officeID;
	}
	private	function validateShipAddrLine1($address)
	{ 
		if (isset($address->ShipAddr->Line1)) 
		{
			return $address->ShipAddr->Line1;
		}
		return "unknown";//"unknown" is returned so that an address object will be created even if there is no address data given
	}
	private	function validateShipAddrLine2($address)
	{ 
		if (isset($address->ShipAddr->Line2)) 
		{
			return $address->ShipAddr->Line2;
		}
		return null;
	}
	private	function validateShipAddrCity($city)
	{ 
		if (isset($city->ShipAddr->City)) 
		{
			return $city->ShipAddr->City;
		} 
		return "unknown";//"unknown" is returned so that an address object will be created even if there is no address data given
	}
	private	function validateShipCountrySubDivisionCode($stateAbrv)
	{ 
		if (isset($stateAbrv->ShipAddr->CountrySubDivisionCode)) 
		{
			//NOTE: need to add code to validate different variations of State abbreviation & select for State name as well		
			return State::where('stabrv',$stateAbrv->ShipAddr->CountrySubDivisionCode)->value('state_id');
		} 
		return null; 
	}
	private	function validateShipAddrPostalCode($zipcode)
	{ 
		if (isset($zipcode->ShipAddr->PostalCode)) 
		{
			return $zipcode->ShipAddr->PostalCode;
		}
		return "unknown";//"unknown" is returned so that an address object will be created even if there is no address data given
	}
	
	/*Validate Appointment Object - when mapping QuickBooks to BlueCard*/
	private function validatePaymentMethod($pymtID)
	{
		if (isset($pymtID->PaymentMethodRef->value))
		{	
			return PaymentMethod::where('pymtqbid',$pymtID->PaymentMethodRef->value)->value('paymentmethod_id');
		}
		return null;
	}
	
	/*Validate ServiceQuote Object - when mapping QuickBooks to BlueCard*/
	private	function validateQBNotes($notes)
	{ 
		if (isset($notes->Notes)) 
		{
			return $notes->Notes;
		}
		return "No notes at this time.  Would you like to add some?";
	}
	

}
?>