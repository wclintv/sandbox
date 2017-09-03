<?php
namespace App\Plugins\QuickbooksOnline\Models;

use App\Plugins\QuickbooksOnline\QuickbooksOnline;
use App\Plugins\QuickbooksOnline\Models\BatchItem;
use App\Models\CustomerData;
use App\Models\CustomerDataList;
use App\Models\Customer;
use App\Models\Address;
use App\Models\Appointment;
use App\Models\ServiceQuote;
use App\Models\Office;
use App\Models\State;
use App\Models\Suffix;
use App\Models\PaymentMethod;

class BatchItemRequest
{
	//QuickBooks Properties
	public $BatchItemRequest = array();
	
	//Methods
	public function SyncToQB(Office $office, CustomerDataList $custDataList)
	{
			
		self::MapBCtoQB($custDataList); //Maps CustomerDataList into a BatchItemRequest
		
		/*Calls QBs with a BatchItemRequest to either update or create records based on mapping & Returns the BatchItemResponse JSON string*/
		return self::UpdateCustomerList($office); 
	}
	private function MapBCtoQB($custDataList)
	{
		//This is a CONVERSION/MAPPING function, it converts/maps customer data from the BC structure to the QuickBooks structure
		$recordCount = 0; //Doesn't look like we are currently using this count
		
		$custID = null;
		$billAddrID = null;
		$shipAddrID = null;
		
		foreach ($custDataList->CustomerDataList as $custDataRecord)
		{
			
			/*QuickBooks BatchItemRequest Object*/
			$batchItm = new BatchItem;
			$custID = $custDataRecord->Customer->cust_id; //This will allow us to update the correct cust record with the BatchItemResponse; see line 179 below
			$batchItm->operation = self::validateOperation($custDataRecord->Customer->qbid); //This will determine if BlueCard is updating or creating in QuickBooks
			
			/*QuickBooks Address Object*/
			
			//QBs BillAddr ID will be checked once we move billing address out of Customer object and into Address object
			//Currently we're not storeing this ID in BlueCard but the QBs BatchItemRequest will still update other Billing Address attributes without the ID
			unset($batchItm->Customer->BillAddr->Id); 
			
			$batchItm = self::mapBillingAddr($batchItm, $custDataRecord->Customer);
		
			foreach($custDataRecord->AddressList as $addrs){ 
				//This will handle one of multiple addresses in CustomerData object
				if ($addrs->adrqbsync == 1 && $addrs->adrisactive == 1)//if an existing address is marked for QBsync and is active then 
				{ 
					//NOTE: remove the empty check on BillAddr once billing address is removed from the Customer table and inculuded in address table
					if ($addrs->adrisbilling == 1 && empty($batchItm->Customer->BillAddr->Line1))//if its marked as a billing addresss assign it to billing 
					{ 
						$billAddrID = $addrs->address_id; //This will allow us to update the correct billing address record with the BatchItemResponse
						if(isset($addrs->adrqbid))
						{
							$batchItm->Customer->BillAddr->Id = $addrs->adrqbid;
						} else 
						{ 
							unset($batchItm->Customer->BillAddr->Id); 
						}	
						
						$batchItm = self::mapBillingAddr($batchItm, $addrs);
							
					} else  //else assign it to shipping	
					{
						$shipAddrID = $addrs->address_id; //This will allow us to update the correct shipping address record with the BatchItemResponse
						if(isset($addrs->adrqbid))
						{
							$batchItm->Customer->ShipAddr->Id = $addrs->adrqbid;
						} else 
						{ 
							unset($batchItm->Customer->ShipAddr->Id); 
						}	
						
						$batchItm = self::mapShippingAddr($batchItm, $addrs);
					}
					//For now assign the billing address to shipping if it is not already populated for testing purposes; remove this later
					if (empty($batchItm->Customer->ShipAddr->Line1)) 
					{
						$shipAddrID = $addrs->address_id; //This will allow us to update the correct shipping address record with the BatchItemResponse
						if(isset($addrs->adrqbid))
						{
							$batchItm->Customer->ShipAddr->Id = $addrs->adrqbid;
						} else 
						{ 
							unset($batchItm->Customer->ShipAddr->Id); 
						}	
						
						$batchItm = self::mapShippingAddr($batchItm, $addrs);		
					}
				} else //for now write the addess even if its not marked for QBsycn or is not marked active 
				{      //but we need to write BlueCard logic to select one shipping and one billing address to be synced
				         
					if ($addrs->adrisbilling == 1) //if its marked as a billing addresss assign it to billing
					{
						$billAddrID = $addrs->address_id; //This will allow us to update the correct billing address record with the BatchItemResponse
						if(isset($addrs->adrqbid))
						{
							$batchItm->Customer->BillAddr->Id = $addrs->adrqbid;
						} else 
						{ 
							unset($batchItm->Customer->BillAddr->Id); 
						}	
						
						$batchItm = self::mapBillingAddr($batchItm, $addrs);
						
					} else //else assign it to shipping
					{ 	
						$shipAddrID = $addrs->address_id; //This will allow us to update the correct shipping address record with the BatchItemResponse	
						if(isset($addrs->adrqbid))
						{
							$batchItm->Customer->ShipAddr->Id = $addrs->adrqbid;
						} else 
						{ 
							unset($batchItm->Customer->BillAddr->Id); 
						}

						$batchItm = self::mapShippingAddr($batchItm, $addrs);	
					}	
					//For now assign the billing address to shipping if it is not already populated for testing purposes; remove this later
					if (empty($batchItm->Customer->ShipAddr->Line1)) 
					{
						$shipAddrID = $addrs->address_id; //This will allow us to update the correct shipping address record with the BatchItemResponse
						if(isset($addrs->adrqbid))
						{
							$batchItm->Customer->ShipAddr->Id = $addrs->adrqbid;
						} else 
						{ 
							unset($batchItm->Customer->ShipAddr->Id); 
						}	
						
						$batchItm = self::mapShippingAddr($batchItm, $addrs);
					}
				}
			}	
			/*QuickBooks ServiceQuote Object - if it had one*/
			foreach($custDataRecord->ServiceQuoteList as $srvqte)
			{ 
				if ($srvqte->qteqbsync = 1 && $srvqte->qteisactive = 1)//This will handle multiple Service Quotes in CustomerData object
				{ 
					$batchItm->Customer->Notes = self::validateBCNotes($srvqte->notes);
				}
				else 
				{ //for now do it anyway but we need to write BlueCard logic to select one service quote to be synched
					$batchItm->Customer->Notes = self::validateBCNotes($srvqte->notes);
				}	
			}	
			/*QuickBooks Appointment Object - if it had one*/
			foreach($custDataRecord->AppointmentList as $appt)
			{ 
				//unset all attributes of the PaymentMethod except $value to avoid throwing errors
				//NOTE: It may be worthwhile in the future to make a QBPaymentMethodRef object in addition to QBPaymentMethod, I'm not sure
				unset(
					$batchItm->Customer->PaymentMethodRef->Name,
					$batchItm->Customer->PaymentMethodRef->Active,
					$batchItm->Customer->PaymentMethodRef->Type,
					$batchItm->Customer->PaymentMethodRef->domain,
					$batchItm->Customer->PaymentMethodRef->sparse,
					$batchItm->Customer->PaymentMethodRef->Id,
					$batchItm->Customer->PaymentMethodRef->SyncToken,
					$batchItm->Customer->PaymentMethodRef->MetaData
				);
				if ($appt->aptqbsync = 1 && $appt->aptisactive = 1 && isset($appt->aptpaymentmethod_id))//This will handle multiple Appointments in CustomerData object
				{
					$batchItm->Customer->PaymentMethodRef->value = self::validatePaymentMethod($appt->aptpaymentmethod_id);
				}
				elseif(isset($appt->aptpaymentmethod_id))//for now do it anyway but we need to write BlueCard logic to select one Appointment to be synched
				{ 
					$batchItm->Customer->PaymentMethodRef->value = self::validatePaymentMethod($appt->aptpaymentmethod_id);
				} else
				{
					unset($batchItm->Customer->PaymentMethodRef);//QBs will throw an error if this is submitted with a null
				}	
			}	
			/*QuickBooks Customer Object*/
			if(isset($custDataRecord->Customer->parentqbid))
			{
				$batchItm->Customer->Job = true;
				$batchItm->Customer->BillWithParent = true; //NOTE: we need to start storing this value in BC so it can be either true or false
				$batchItm->Customer->ParentRef->value = $custDataRecord->Customer->parentqbid;
			} 
			else 
			{
				unset($batchItm->Customer->ParentRef->value, $batchItm->Customer->Job, $batchItm->Customer->BillWithParent);
			}	
			
			if(isset($custDataRecord->Customer->qbid))
			{
				$batchItm->Customer->Id = $custDataRecord->Customer->qbid;
			} 
			else
			{
				unset($batchItm->Customer->Id); //If you do not remove the QBs customer Id on a create then it will error out 
			} 
			
			$batchItm->Customer->SyncToken = $custDataRecord->Customer->qbsynctoken; //You must include the SyncToken with updates
			$batchItm->Customer->Title = $custDataRecord->Customer->title;
			$batchItm->Customer->GivenName = $custDataRecord->Customer->firstname; // . rand();//assigns a random number to the bid so that it will be unique for dev purposes;
			$batchItm->Customer->MiddleName = $custDataRecord->Customer->middlename;
			$batchItm->Customer->FamilyName = $custDataRecord->Customer->lastname; // . rand();
			//$batchItm->Customer->Suffix = self::validateCustSuffixID($custDataRecord);//This is for the original BC custsuffix_id validation
			$batchItm->Customer->Suffix = $custDataRecord->Customer->suffix;
			$batchItm->Customer->CompanyName = $custDataRecord->Customer->company;
			
			$batchItm->Customer->PrimaryPhone->FreeFormNumber = $custDataRecord->Customer->phone;
			$batchItm->Customer->AlternatePhone->FreeFormNumber = $custDataRecord->Customer->altphone;
			$batchItm->Customer->Mobile->FreeFormNumber = $custDataRecord->Customer->mobilephone;
			$batchItm->Customer->Fax->FreeFormNumber = $custDataRecord->Customer->fax;
			
			$batchItm->Customer->PrimaryEmailAddr->Address = $custDataRecord->Customer->email;
			$batchItm->Customer->WebAddr->URI = $custDataRecord->Customer->website;
			
			
			//This will allow us to keep track of all three BC id's when we are matching the newly created QBs id's in the BatchItemResponse update();
			$batchItm->bId = $custID . "&". $billAddrID . "&" . $shipAddrID; 
			
			$this->BatchItemRequest[] = $batchItm;
			$recordCount = $recordCount + 1;
			unset($batchItm, $custDataRecord); //"destroys" object to help with memory limit
		}	
		return $recordCount;
	}	
	public function EchoJson()
	{
		return json_encode($this);
	}
	/*Map Billing Address*/
	private function mapBillingAddr($batchAddr, $custAddr)
	{
		if(!empty($custAddr->billingaddress1))
		{	
			$batchAddr->Customer->BillAddr->Line1 = $custAddr->billingaddress1;
			$batchAddr->Customer->BillAddr->Line2 = $custAddr->billingaddress2;
			$batchAddr->Customer->BillAddr->City = $custAddr->billingcity;
			$batchAddr->Customer->BillAddr->CountrySubDivisionCode = self::validateAddrStateID($custAddr->billingstate_id);
			$batchAddr->Customer->BillAddr->PostalCode = $custAddr->billingzipcode;
		} elseif(!empty($custAddr->address1))
		{	
			$batchAddr->Customer->BillAddr->Line1 = $addrs->address1;
			$batchAddr->Customer->BillAddr->Line2 = $addrs->address2;
			$batchAddr->Customer->BillAddr->City = $addrs->city;	
			$batchAddr->Customer->BillAddr->CountrySubDivisionCode = self::validateAddrStateID($custAddr->adrstate_id);
			$batchAddr->Customer->BillAddr->PostalCode = $addrs->zipcode;
		}
		
		$batchAddr->Customer->BillAddr->Country = "USA";
		
		return $batchAddr;
	}
	/*Map Shipping Address*/
	private function mapShippingAddr($batchAddr, $custAddr)
	{
		$batchAddr->Customer->ShipAddr->Line1 = $custAddr->address1;
		$batchAddr->Customer->ShipAddr->Line2 = $custAddr->address2;
		$batchAddr->Customer->ShipAddr->City = $custAddr->city;
		$batchAddr->Customer->ShipAddr->Country = "USA";
		$batchAddr->Customer->ShipAddr->CountrySubDivisionCode = self::validateAddrStateID($custAddr->adrstate_id);
		$batchAddr->Customer->ShipAddr->PostalCode = $custAddr->zipcode;
		
		return $batchAddr;
	}
	
	/*Validate BatchItemRequest Object - when mapping BlueCard to QuickBooks*/
	private function validateOperation($qbid)
	{
		if (!empty($qbid)){
			return "update";
		} else { return "create"; }
	}
	/*Validate Address Object - when mapping BlueCard to QuickBooks*/
	private	function validateAddrStateID($stateID)
	{ 
		if (!empty($stateID)) 
		{
			$state = State::find($stateID);
			return $state->stabrv;
		}
		return "";
	}
	/*Validate ServiceQuote Object - when mapping BlueCard to QuickBooks*/
	private function validateBCNotes($notes)
	{
		$append = "&hellip;";
		$length = 2000; //max allowed by QBs
		$string = trim($notes);
		if (strlen($string) > $length){
			$string = wordwrap($string, $length);
			$string = explode("\n", $string, 2);
			$string = $string[0] . $append;
		} 
		return $string;
	}
	/*Validate Appointment Object - when mapping BlueCard to QuickBooks*/
	private function validatePaymentMethod($pymtID)
	{
		if (!empty($pymtID))
		{
			$pymtMethod = PaymentMethod::find($pymtID);//this find works because PaymentMethod is an actual table as opposed to CustomerData
			return $pymtMethod->pymtqbid;
		}
		return 0;
	}
	/*Validate CustomerData Object - when mapping BlueCard to QuickBooks*/
	
	/*private	function validateBillingStateID($custDataRecord)
	{ 
		if (!empty($custDataRecord->Customer->billingstate_id)) 
		{
			$state = State::find($custDataRecord->Customer->billingstate_id);
			return $state->stabrv;
		}
		return "";
	}
	*/
	private function validateCustSuffixID($custDataRecord)
	{
		if (!empty($custDataRecord->Customer->custsuffix_id)) 
		{	
			$suffix = Suffix::find($custDataRecord->Customer->custsuffix_id);
			return $suffix->suffix;
		}
		return "";
	
	}
	/*Database QuickBooks Calls*/
	private function UpdateCustomerList(Office $office)
	{
		//set the url endpoint for REST call.
		$url = "batch";
		//get response from quickbooks
		return QuickbooksOnline::post($url, $this->EchoJson(), $office);//array returned
	}
}
?>