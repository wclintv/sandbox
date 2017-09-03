<?php
namespace App\Plugins\QuickbooksOnline\Models;

use Auth;
use App\Plugins\QuickbooksOnline\Helpers\DateTimeConverter;
use App\Plugins\QuickbooksOnline\QuickbooksOnline;
use App\Plugins\QuickbooksOnline\Models\BatchItem;
use App\Plugins\QuickbooksOnline\Models\QBAddress;
use App\Plugins\QuickbooksOnline\Models\QBEmail;
use App\Plugins\QuickbooksOnline\Models\QBWebAddress;
use App\Plugins\QuickbooksOnline\Models\QBCurrencyRef;
use App\Plugins\QuickbooksOnline\Models\QBDefaultTaxCodeRef;
use App\Plugins\QuickbooksOnline\Models\QBMetaData;
use App\Plugins\QuickbooksOnline\Models\QBParentRef;
use App\Plugins\QuickbooksOnline\Models\QBPhone;
use App\Plugins\QuickbooksOnline\Models\QBPaymentMethod;
use App\Models\Address;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\CustomerData;
use App\Models\Office;
use App\Models\ServiceQuote;
use App\Models\State;
use App\Models\Suffix;
use App\Models\PaymentMethod;

class QBCustomer
{
	private static $startposition;
	private static $count;
	private static $setsize;
	private static $sets;

	//Constructor
	function __construct()
	{
		$this->BillAddr = new QBAddress;
		$this->ShipAddr = new QBAddress;
		$this->ParentRef = new QBParentRef;
		$this->PaymentMethodRef = new QBPaymentMethod;
		$this->CurrencyRef = new QBCurrencyRef;
		$this->MetaData = new QBMetaData;
		$this->PrimaryPhone = new QBPhone;
		$this->AlternatePhone = new QBPhone;
		$this->Mobile = new QBPhone;
		$this->Fax = new QBPhone;
		$this->PrimaryEmailAddr = new QBEmail;
		$this->WebAddr = new QBWebAddress;
		$this->DefaultTaxCodeRef = new QBDefaultTaxCodeRef;
	}

	//Properties
	public $Taxable;
	public $BillAddr = array(); //MAPPED!
	public $ShipAddr = array(); //MAPPED!
	public $Notes;				//MAPPED!
	public $Job;				//MAPPED!
	public $BillWithParent;		//MAPPED!
	public $ParentRef = array();//MAPPED!
	public $PaymentMethodRef = array(); //MAPPED
	public $Level;
	public $Balance;
	public $BalanceWithJobs;
	public $CurrencyRef = array();
	public $PreferredDeliveryMethod;
	public $ResaleNum;
	public $domain;
	public $sparse;
	public $Id;					//MAPPED!
	public $SyncToken;			//MAPPED!	
	public $MetaData = array();
	public $Title;				//MAPPED!
	public $GivenName;			//MAPPED!
	public $MiddleName;			//MAPPED!
	public $FamilyName;			//MAPPED!
	public $Suffix;				//MAPPED!
	public $FullyQualifiedName;
	public $CompanyName;        //MAPPED!
	public $DisplayName;
	public $PrintOnCheckName;
	public $Active;
	public $PrimaryPhone = array();//MAPPED!
	public $AlternatePhone = array();//MAPPED!
	public $Mobile = array();     //MAPPED!	
	public $Fax = array();        //MAPPED!
	public $PrimaryEmailAddr = array();//MAPPED!
	public $WebAddr = array();    //MAPPED!
	public $DefaultTaxCodeRef = array();
	
	
	public static function all(Office $office)
	{
		//QuickBooks will only allow a maximum of 1000 records returned per query response so we will have to get all records through Pagination
		Self::trackers_reset();
		//Call QBs to find out how many total records are in the customer table
		Self::$count = Self::count($office);
		//this will divide the count into sets rounded up...NOTE: we could raise this as high as sets of 1000
		Self::$sets = ceil(Self::$count/Self::$setsize);
		
		//iterate through each set compiling the customers into a master list.
		$qbCustomersList = [];
		for($i = 0; $i < Self::$sets; $i++)
		{
			//get a set list from quickbooks
			$list = Self::get_by_startposition($office, Self::$startposition);

			//Process each set list returned from quickbooks, and add it to the return list.
			foreach($list as $qbCustomer)
			{
				$c = new QBCustomer;
				$c->fill($qbCustomer);
				$qbCustomersList[] = $c;
			}
			Self::$startposition += Self::$setsize;
		}
		//returns a list of QBCustomer
		return $qbCustomersList;		
	}
	public static function count(Office $office)
	{
		$query = "SELECT COUNT(*) FROM Customer";
		$array = QuickbooksOnline::get($query, $office);
		if(isset($array['QueryResponse']))
		{
			return $array['QueryResponse']['totalCount'];
		}
		return $array;
	}
	public static function find(Office $office, $id) //Get function
	{
		$query = "SELECT * FROM Customer WHERE Id='" . $id . "'";
		$array = QuickbooksOnline::get($query, $office);
		if(isset($array['QueryResponse']))
		{
			return $array['QueryResponse']['Customer'][0];
		}
		if(isset($array['Fault']))
		{
			return "fault";
		}
		return $array;
	}
	private static function get_by_startposition(Office $office, $start)
	{
		$query = "SELECT * FROM Customer STARTPOSITION " . $start . " MAXRESULTS " . Self::$setsize;	
		$array = QuickbooksOnline::get($query, $office);
		return $array['QueryResponse']['Customer'];		
	}
	private static function get_by_startposition_timestamp(Office $office, $start, $timestamp)
	{
		$query = "SELECT * FROM Customer WHERE MetaData.LastUpdatedTime >= '" . $timestamp . "' STARTPOSITION " . $start . " MAXRESULTS " . Self::$setsize;
		$array = QuickbooksOnline::get($query, $office);
		return $array['QueryResponse']['Customer'];
	}
	public static function get_update_count(Office $office, $timestamp)
	{
		//Call QBs to find out how many customer records have been updated since last sync
		$query = "SELECT COUNT(*) FROM Customer WHERE MetaData.LastUpdatedTime >= '" . $timestamp . "'";
		$array = QuickbooksOnline::get($query, $office);
		return $array['QueryResponse']['totalCount'];
	}
	public static function get_update_list_from_quickbooks(Office $office, $timestamp)
	{
		Self::trackers_reset();
		Self::$count = Self::get_update_count($office, $timestamp);
		Self::$sets = ceil(Self::$count/Self::$setsize);

		$customers = []; //declares $customer as an array
		for($i = 0; $i < Self::$sets; $i++)
		{
			$list = Self::get_by_startposition_timestamp($office, Self::$startposition, $timestamp);
			foreach($list as $customer)
			{
				$customers[] = $customer;
			}
			Self::$startposition + Self::$setsize;
		}
		return $customers;
	}
	public static function sync($custData_QB, $sync_timestamp)//this function gets data from QBs and Updates existing records in the BC database if they exist, if the record does not exist in BC then it Creates it
	{		
		//	
	}
	public static function from_customerdata(CustomerData $cd)//This is a CONVERSION/MAPPING function, it converts/maps customer data from the BC structure to the QuickBooks structure
	{
		$qb = new QBCustomer;

		//This will allow us to update the correct cust record with the BatchItemResponse; see line 142 below
		$id = $cd->Customer->cust_id; 

		//This will determine if BlueCard is updating or creating in QuickBooks
		$qb->operation = Self::validate_operation($customerdata); 

		//Billing Address
		unset($qb->Customer->BillAddr->Id); //This will be checked once we move billing address out of Customer object and into Address object
		$qb->Customer->BillAddr->Line1 					= $cd->Customer->billingaddress1;
		$qb->Customer->BillAddr->Line2 					= $cd->Customer->billingaddress2;
		$qb->Customer->BillAddr->City 					= $cd->Customer->billingcity;
		$qb->Customer->BillAddr->Country 				= "USA";
		$qb->Customer->BillAddr->CountrySubDivisionCode = self::validate_state_id($cd->Customers->billingstate_id);
		$qb->Customer->BillAddr->PostalCode 			= $cd->Customer->billingzipcode;
			
		//Shipping Address
		$qb->Customer->ShipAddr = QBAddress::from_customerdata($cd);

		//QuickBooks ServiceQuote Object - if it had one
		foreach($cd->ServiceQuoteList as $srvqte)
		{ 
			if ($srvqte->qteqbsync = 1 && $srvqte->qteisactive = 1)
			{ 
				//This will handle multiple service quotes in CustomerData object
				$qb->Customer->Notes = self::validate_notes($srvqte);
			}
			else
			{ 
				//for now do it anyway but we need to write BlueCard logic to select one service quote to be synched
				$qb->Customer->Notes = self::validate_notes($srvqte);
			}	
		}	


		//QuickBooks Customer Object
		if(isset($cd->Customer->parentqbid))
		{
			$qb->Customer->Job = true;
			$qb->Customer->BillWithParent = true; //NOTE: we need to start storing this value in BC so it can be either true or false
			$qb->Customer->ParentRef->value = $cd->Customer->parentqbid;
		} 
		else 
		{
			unset($qb->Customer->ParentRef->value, $qb->Customer->Job, $qb->Customer->BillWithParent);
		}				
		if(isset($cd->Customer->qbid))
		{
			$qb->Customer->Id = $cd->Customer->qbid;
		} 
		else 
		{
			unset($qb->Customer->Id); //If you do not remove the QBs customer Id on a create then it will error out 
		}
		$qb->Customer->SyncToken 						= $cd->Customer->qbsynctoken; //You must include the SyncToken with updates
		$qb->Customer->GivenName 						= $cd->Customer->firstname; // . rand(); //assigns a random number to the bid so that it will be unique for dev purposes;
		$qb->Customer->MiddleName 						= $cd->Customer->middlename;
		$qb->Customer->FamilyName 						= $cd->Customer->lastname; // . rand(); //assigns a random number to the bid so that it will be unique for dev purposes;
		$qb->Customer->Suffix 							= self::validate_suffix_id($cd->Customer->custsuffix_id);			
		$qb->Customer->PrimaryPhone->FreeFormNumber 	= $cd->Customer->phone;
		$qb->Customer->AlternatePhone->FreeFormNumber 	= $cd->Customer->altphone;
		$qb->Customer->PrimaryEmailAddr->Address 		= $cd->Customer->email;

		return $qb;
	}
	public function to_customerdata()//This is a CONVERSION/MAPPING function, it converts/maps customer data from the QBs structure to the BlueCard structure
	{
		//BlueCard CustomerData Object
		$cd = new CustomerData; 
		$cd->Customer->cust_id 			= null; //not stored in QBs
		$cd->Customer->qbid 			= self::validate_string($this->Id);
		$cd->Customer->parentqbid 		= self::validate_parentref($this->ParentRef);//NOTE: "->value" is in the validation			
		$cd->Customer->title			= self::validate_title($this->Title);
		$cd->Customer->firstname 		= self::validate_string($this->GivenName);
		$cd->Customer->middlename 		= self::validate_string($this->MiddleName);
		$cd->Customer->lastname 		= self::validate_string($this->FamilyName);		
		//$cd->Customer->custsuffix_id 	= self::validate_suffix($this->Suffix); //NOTE: Don't need this for release 1.0
		$cd->Customer->suffix			= self::validate_suffix_string($this->Suffix);
		
		$cd->Customer->phone 			= self::validate_phone($this->PrimaryPhone);//NOTE: "->FreeFormNumber" is in the validation
		$cd->Customer->altphone 		= self::validate_phone($this->AlternatePhone); 
		$cd->Customer->mobilephone		= self::validate_phone($this->Mobile);
		$cd->Customer->fax				= self::validate_phone($this->Fax);
		
		$cd->Customer->company			= self::validate_company($this->CompanyName);
		$cd->Customer->email 			= self::validate_email($this->PrimaryEmailAddr);
		$cd->Customer->website			= self::validate_website($this->WebAddr);
		
		$cd->Customer->balancedue 		= self::validate_string($this->Balance);
		$cd->Customer->qbsynctoken 		= self::validate_string($this->SyncToken);
		$cd->Customer->qbstartdate 		= self::validate_datetime($this->MetaData->CreateTime);
		$cd->Customer->qbtimemodified 	= self::validate_datetime($this->MetaData->LastUpdatedTime);

		$cd->Customer->billingaddress1 	= self::validate_string($this->BillAddr->Line1);
		$cd->Customer->billingaddress2 	= self::validate_address2($this->BillAddr->Line2);
		$cd->Customer->billingcity 		= self::validate_string($this->BillAddr->City);
		$cd->Customer->billingstate_id 	= self::validate_state($this->BillAddr->CountrySubDivisionCode);
		$cd->Customer->billingzipcode 	= self::validate_string($this->BillAddr->PostalCode);


		//NOTE: when we move billing address into the address object we will need to set adrisbilling = 1;
		
		//Address Object
		$addr = new Address; //create a new Address object

		//If the shipping address is null...copy the billing address to shipping address.
		//else...copy shipping to shipping.
		if (is_null($this->ShipAddr))
		{
			$addr->address_id 			= null; //not stored in QBs
			$addr->adrqbid 				= self::validate_address_qbid($this->BillAddr->Id);
			$addr->adroffice_id 		= self::validate_company_id();
			$addr->address1 			= self::validate_string($this->BillAddr->Line1);
			$addr->address2 			= self::validate_address2($this->BillAddr->Line2);
			$addr->city 				= self::validate_string($this->BillAddr->City);
			$addr->adrstate_id 			= self::validate_state($this->BillAddr->CountrySubDivisionCode);
			$addr->zipcode 				= self::validate_string($this->BillAddr->PostalCode);
			$addr->adrisbilling 		= 0; 
			$addr->adrqbsync			= 1; //Only one Address record for any cust_id/address_id combination is allowed to be selected for qbsync at a time; this can only be assigned here in the QBtoBC sync
			$addr->adrisactive 			= 1;
		} 
		else 
		{ 
			$addr->address_id 			= null; //not stored in QBs
			$addr->adrqbid 				= self::validate_address_qbid($this->ShipAddr->Id);
			$addr->adroffice_id 		= self::validate_company_id(); 
			$addr->address1 			= self::validate_string($this->ShipAddr->Line1);
			$addr->address2 			= self::validate_address2($this->ShipAddr->Line2);
			$addr->city 				= self::validate_string($this->ShipAddr->City);
			$addr->adrstate_id 			= self::validate_state($this->ShipAddr->CountrySubDivisionCode);
			$addr->zipcode 				= self::validate_string($this->ShipAddr->PostalCode);
			$addr->adrisbilling 		= 0;
			$addr->adrqbsync 			= 1; //Only one Address record for any cust_id/address_id combination is allowed to be selected for qbsync at a time; this can only be assigned here in the QBtoBC sync
			$addr->adrisactive 			= 1;
		}	
		$cd->AddressList[] = $addr; //load Address object into CustomerData object			
		unset($addr); //"destroys" object to help with memory limit
		

		//Appointment Object
		$appt = new Appointment;//create a new Appointment object
		$appt->aptqbsync = 1; //Only one Appointment record for any cust_id/address_id combination is allowed to be selected for qbsync at a time; this can only be assigned here in the QBtoBC sync
		$appt->aptisactive = 1; //This will activate an Appointment object and cause it to be added to the CustomerData record
		$appt->aptpaymentmethod_id = self::validate_PaymentMethod($this->PaymentMethodRef);//NOTE: "->value" is in the validation
		$cd->AppointmentList[] = $appt; //load Appointment object into CustomerData object
		unset($appt); //"destroys" object to help with memory limit
		

		//ServiceQuote Object
		$srvqte = new ServiceQuote;//create a new ServiceQuote object
		$srvqte->qtefreq_id = 8; //This field is required by BC and will activate a ServiceQuote object and cause it to be added to the CustomerData record
		$srvqte->qteservicetime_id = 5; //This field is required by BC
		$srvqte->notes = self::validate_notes($this->Notes);
		$srvqte->qteqbsync = 1; //Only one of many ServiceQuotes associated with any one Appointment record is allowed to be selected for qbsync at a time; this can only be assigned here in the QBtoBC sync 
		$srvqte->qteisactive = 1;
		$cd->ServiceQuoteList[] = $srvqte; //load ServiceQuote object into CustomerData object
		unset($srvqte); //"destroys" object to help with memory limit

		return $cd;
	}
	public function toArray()
	{
		return json_decode($this, true);
	}
	public static function trackers_reset()
	{
		Self::$startposition = 1;
		Self::$count = null;
		Self::$setsize = 1000; //max 1000
		Self::$sets = null;
	}
	public static function trackers_report()
	{
		$result = [
			'startposition' => Self::$startposition,
			'count' 		=> Self::$count,
			'setsize'       => Self::$setsize,
			'sets'			=> Self::$sets,
		];
		return $result;
	}	
	private function fill(Array $qbCustomer)
	{
		foreach($qbCustomer as $key => $value)
		{
			switch($key)
			{
				case "AlternatePhone":
					$p = new QBPhone;
					$p->fill($value);
					$this->AlternatePhone = $p;
					break;		
				case "BillAddr":
					$a = new QBAddress;
					$a->fill($value);
					$this->BillAddr = $a;
					break;
				case "CurrencyRef":
					$c = new QBCurrencyRef;
					$c->fill($value);
					$this->CurrencyRef = $c;
					break;
				case "DefaultTaxCodeRef":
					$d = new QBDefaultTaxCodeRef;
					$d->fill($value);
					$this->DefaultTaxCodeRef = $d;
					break;
				case "Fax":									
					$p = new QBPhone;
					$p->fill($value);
					$this->Fax = $p;
					break;	
				case "MetaData":
					$m = new QBMetaData;
					$m->fill($value);
					$this->MetaData = $m;
					break;
				case "Mobile":
					$p = new QBPhone;
					$p->fill($value);
					$this->Mobile = $p;	
					break;
				case "ParentRef":
					$p = new QBParentRef;
					$p->fill($value);
					$this->ParentRef = $p;
					break;
				case "PrimaryPhone":
					$p = new QBPhone;
					$p->fill($value);
					$this->PrimaryPhone = $p;
					break;
				case "PrimaryEmailAddr":
					$e = new QBEmail;
					$e->fill($value);
					$this->PrimaryEmailAddr = $e;
					break;
				case "ShipAddr":
					$a = new QBAddress;
					$a->fill($value);
					$this->ShipAddr = $a;
					break;
				case "WebAddr":
					$w = new QBWebAddress;
					$w->fill($value);
					$this->WebAddr = $w;
					break;
				case "PaymentMethodRef":
					$p = new QBPaymentMethod;
					$p->fill($value);
					$this->PaymentMethodRef = $p;
					break;
				default:
					$this->$key = $value;
			}			
		}
	}
	
	/////////////////////////////////VALIDATION////////////////////////////////
	private function validate_company_id()
	{
		return Auth::user()->qbo_membership->office->office_id;
		//return  Office::where('qb_realmid', Auth::user()->qb_realmid)->value('office_id');
	}	
	private function validate_state_id($state_id)
	{
		if($state_id != null)
		{
			$brv = State::find($state_id)->value('stabrv');
			return $brv;
		}
		return null;
	}
	private function validate_datetime($value)
	{
		return DateTimeConverter::to_datetime_from_quickbooks($value);
	}
	private function validate_title($title)
	{
		if($title != null)
		{
			return $title;
		}
		return null;
	}
	private function validate_company($company)
	{
		if($company != null)
		{
			return $company;
		}
		return null;
	}
	private function validate_email(QBEmail $value)
	{
		if($value != null)
		{
			return $value->Address;
		}
		return null;
	}
	private function validate_website(QBWebAddress $value)
	{
		if($value != null)
		{
			return $value->URI;
		}
		return null;
	}
	private static function validate_operation(CustomerData $customerdata)
	{
		//Validate BatchItemRequest Object - when mapping BlueCard to QuickBooks
		if (!empty($customerdata->Customer->qbid))
		{
			return "update";
		} 
		return "create";
	}	
	private static function validate_parentref(QBParentRef $ref)
	{
		if($ref != null)
		{
			return $ref->value;
		}
		return null;
	}
	private function validate_phone(QBPhone $value)
	{
		if($value != null)
		{
			return $value->FreeFormNumber;
		}
		return null;	
	}
	private function validate_string($value)
	{
		if($value != null)
		{
			return $value;
		}
		return "unknown";
	}
	private function validate_address_qbid($value)
	{
		if($value != null)
		{
			return $value;
		}
		return null;
	}
	private function validate_suffix($suffix_name)
	{
		if($suffix_name != null)
		{
			return Suffix::where('suffix', $suffix_name)->first()->value('suffix_id');
		}
		return null;
	}
	private function validate_suffix_id($suffix)
	{
		if($suffix  != null)
		{
			return Suffix::find($suffix_id)->value('suffix');
		}
		return null;
	}
	private function validate_suffix_string($suffix)
	{
		if($suffix  != null)
		{
			return $suffix;
		}
		return null;
	}
	private function validate_address2($address)
	{
		if($address  != null)
		{
			return $address;
		}
		return null;
	}
	private function validate_state($state_abrv)
	{
		if($state_abrv != null)
		{
			return State::where('stabrv', $state_abrv)->value('state_id');
		}
		return null;
	}
	private function validate_PaymentMethod($pymtID)
	{
		if (isset($pymtID->value))
		{	
			return PaymentMethod::where('pymtqbid',$pymtID->value)->value('paymentmethod_id');
		}
		return null;
	}
	private function validate_notes($notes)
	{
		$string = null;
		//Validate ServiceQuote Object - when mapping BlueCard to QuickBooks
		$append = "&hellip;";
		$length = 2000; //max allowed by QBs
		$string = trim($notes);
		if (strlen($string) > $length)
		{
			$string = wordwrap($string, $length);
			$string = explode("\n", $string, 2);
			$string = $string[0] . $append;
		} 
		return $string;
	}	

}
?>