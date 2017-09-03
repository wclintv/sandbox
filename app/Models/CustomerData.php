<?php
namespace App\Models;

use Schema;
use DB;
use DateTime;
use App\Models\Customer;
use App\Models\CustomerSearchData;
use App\Models\Address;
use App\Models\Appointment;
use App\Models\ServiceQuote;
use App\Models\Office;
use Illuminate\Http\Request;
use App\Utility\QLog;

class CustomerData
{
	public $Customer;
	public $AddressList = [];
	public $ServiceQuoteList = [];
	public $AppointmentList= [];

	//Constructor
	public function __construct()
	{
		$this->Customer = new Customer;
	}

	//Public Methods
	public static function all()//returns a list of all CustomerData records in the database
	{
		$list = [];

		$ids = Customer::all()->pluck('cust_id');

		foreach($ids as $id)
		{
			$cd = new CustomerData;
			$cd->Customer = Customer::find($id);
			$cd->AddressList = Self::address_list_get($id);
			$cd->AppointmentList = Self::appointment_list_get($id);
			$cd->ServiceQuoteList = Self::servicequote_list_get($id);
			$list[] = $cd;
		}
		return $list;
	}	
	public static function find($cust_id)//returns a single CustomerData record based on cust_id
	{
		$cd = new CustomerData;
		$cd->Customer = Customer::find($cust_id);
		if($cd->Customer != null)
		{
			$cd->AddressList = Self::address_list_get($cust_id);
			$cd->AppointmentList = Self::appointment_list_get($cust_id);
			$cd->ServiceQuoteList = Self::servicequote_list_get($cust_id);
			return $cd;
		}
		return null;
	}
	public static function update(Array $array)
	{
		//NOTE: Currently the CustomerController runs update on a CustomerData record with a single address, appointment, and serivcequote.
		
		if(!empty($array['Customer']['cust_id']))
		{
			$cd = CustomerData::find($array['Customer']['cust_id']);
			$cd->Customer->update($array['Customer']);
		}	
		
		$addrCount = count($array['AddressList']);
		for($x = 0; $x < $addrCount; $x++) 
		{	
			if(!empty($array['AddressList'][$x]['address_id']))
			{
				$cd->AddressList[$x]->update($array['AddressList'][$x]);
			}	
		}
		$apptCount = count($array['AppointmentList']);
		for($x = 0; $x < $apptCount; $x++) 
		{
			if(!empty($array['AppointmentList'][$x]['appointment_id']))
			{
				$cd->AppointmentList[$x]->update($array['AppointmentList'][$x]);
			}	
		}		
		$servCount = count($array['ServiceQuoteList']);
		for($x = 0; $x < $servCount; $x++) 
		{
			if(!empty($array['ServiceQuoteList'][$x]['servicequote_id']))
			{
				$cd->ServiceQuoteList[$x]->update($array['ServiceQuoteList'][$x]);
			}
		}	
			
		//Update a customersearch data entry
		if(!empty($cd->Customer->cust_id))//this verifies that a cust_id is populated 
		{	
			if($csd = CustomerSearchData::find($cd->Customer->cust_id))//if the Customer record exists, Update it 
			{
				$csd->fill_from_customerdata($cd);
				$csd->save();
			} else//if the Customer record doesn't exist, Create it 
			{	
				//NOTE: This create option is necessary on the CustomerData update function because Cory's Customer.create function does not write to the CustomerSearchData table 
				//CustomerData object is passed because the create function maps it to CustomerSearchData
				CustomerSearchData::create($cd);
			}
		}	
		return $cd;
	}	
	public static function create(Array $array)
	{
		//if a new Customer or Address is created then the trigger will be set to TRUE and a new Appointment must be created
		$trigger = FALSE;
		
		$cd = new CustomerData;
		
		/*if the cust_id is given, use it; otherwise create a new Customer*/
		if(!empty($array['Customer']['cust_id'])) 
		{
			//NOTE: Since we don't need to create a new customer record, I'm just capturing the pieces needed to 
			//update the CustomerSearchData entry below
			$cd->Customer->cust_id = $array['Customer']['cust_id'];
			$cd->Customer->firstname = $array['Customer']['firstname'];
			$cd->Customer->lastname = $array['Customer']['lastname'];
			$cd->Customer->phone = $array['Customer']['phone'];
			$cd->Customer->altphone = $array['Customer']['mobilephone'];
		} else 
		{
			$cd->Customer = Customer::create($array['Customer']);
			$trigger = TRUE;
		}
		
		/*if the address_id is given, use it; otherwise create a new Address*/
		foreach($array['AddressList'] as $address)
		{
			if(!empty($address['address_id'])) 
			{
				$addr = new Address;
				
				//NOTE: Since we don't need to create a new address record, I'm just capturing the pieces needed to 
				//update the CustomerSearchData entry below
				$addr->address_id = $address['address_id'];
				$addr->adroffice_id = $address['adroffice_id'];
				$addr->address1 = $address['address1'];
				
				$cd->AddressList[] = $addr;
				unset($addr);
			} else 
			{
				$cd->AddressList[] = Address::create($address);
				$trigger = TRUE;
			}
		}
		
		/*combine the cust_id & address_id to create a new Appointment*/
		foreach($array['AppointmentList'] as $appt)
		{
			$ap = new Appointment;
			
			if(!empty($appt->appointment_id) && $trigger == FALSE) 
			{
				//OPTION 1: if appointment_id was given AND no new Customer OR Address were created, use it
				$ap->appointment_id = $appt['appointment_id'];
				$cd->AppointmentList[] = $ap;
				
			} elseif(empty($appt->appointment_id) && $trigger == FALSE) 
			{
				//OPTION 2: if cust_id & address_id were given but appointment_id was NOT given, find the existing appointment_id and use it
				$ap->appointment_id = Appointment::where('aptcust_id',$cd->Customer->cust_id)
											->where('aptaddress_id',$cd->AddressList[0]['address_id'])
											->value('appointment_id');

				$cd->AppointmentList[] = $ap;	
				
			} elseif($trigger == TRUE) 
			{
				//OPTION 3: if a new Customer OR a new Address was created; doesn't matter if an appointment_id was given, 
				//a new Appointment MUST be made so remove any existing appointment_id
			
				unset($appt['appointment_id']); 
				
				//Appointments require a customer and address id.
				$appt['aptcust_id'] = $cd->Customer->cust_id;
				$appt['aptaddress_id'] = $cd->AddressList[0]->address_id;
				//$appt['aptpaymentmethod_id'] = $cd->AppointmentList[0]->aptpaymentmethod_id;
				
				//This is not allowing a new appoitment record created with a new addressID and the same customerID as another
				//appointment record, I think it has something to do with the "hasOne" in the customer function of the Appointment object
				//this should be fine for V1 but needs to be reviewed in V2; it needs to be "belongsToMany" like in the ServiceQuote object
				$cd->AppointmentList[] = Appointment::create($appt);
				
				$trigger = TRUE;
			}
			unset($ap);
		}

		/*create a new ServiceQuote*/
		foreach($array['ServiceQuoteList'] as $sq)
		{
			//ServiceQuotes require an appointment id.
			$sq['qteappointment_id'] = $cd->AppointmentList[0]->appointment_id;
			
			$cd->ServiceQuoteList[] = ServiceQuote::create($sq);
		}

		/*create a customersearch data entry*/
		if(!empty($cd->Customer->cust_id))//this verifies that a cust_id is populated 
		{	
			if($csd = CustomerSearchData::find($cd->Customer->cust_id))//if the Customer record exists, Update it 
			{
				$csd->fill_from_customerdata($cd);
				$csd->save();
			} else//if the Customer record doesn't exist, Create it 
			{	
				//CustomerData object is passed because the create function maps it to CustomerSearchData
				CustomerSearchData::create($cd);
			}
		}
		
		return $cd;
	}
	public static function deleteall($officeID)
	{
		$sq = DB::table('servicequote')
						->join('appointment', 'appointment.appointment_id', '=', 'servicequote.qteappointment_id')
						->join('address', 'address.address_id', '=', 'appointment.aptaddress_id')
						->where('adroffice_id', '=', $officeID)
						->delete();			
						
		$cu = DB::table('customer')
						->join('appointment', 'appointment.aptcust_id', '=', 'customer.cust_id')
						->join('address', 'address.address_id', '=', 'appointment.aptaddress_id')
						->where('adroffice_id', '=', $officeID)
						->delete();
						
		$ad = DB::table('address')
						->where('adroffice_id', '=', $officeID)
						->delete();				
		
		//NOTE: There is a DELETE CASCADE on the Appointment table for cust_id and address_id that will automatically 
		//delete the Appointment records that are associated with the deleted Customer and Address records
		/*	
		$ap = DB::table('appointment')
						->join('address', 'address.address_id', '=', 'appointment.aptaddress_id')
						->where('adroffice_id', '=', $officeID)
						->delete();
		*/	
		
		if($sq && $cu && $ad)
		{
			return 1;
		}		
		return 0;
	}
	public function sync()//To sync is to choose either to update or create each record sent
	{
		//QLog::output('Made it into CustomerData Sync');
		//QLog::output($this->toArray());
		
		if (self::SyncCustomerData($this->Customer->cust_id))
		{
			//It doesn't matter if the address already exsists, if the cutomer does not exist then a create must be done
			//But what if the cutomer exists but their is no address???
			
			self::Update($this->toArray());
			return "update";
			
		} else 
		{
			return self::Create($this->toArray());
		}
	}
	public function fill(Array $array)//this is loading an array of customer data information into a CustomerData object; it's being used by Update() above
	{
		$this->Customer->fill($array['Customer']);
		$this->AddressList[0]->fill($array['AddressList'][0]);
		$this->ServiceQuoteList[0]->fill($array['ServiceQuoteList'][0]);
		$this->AppointmentList[0]->fill($array['AppointmentList'][0]);
	}
	public function toArray()//this turns a CustomerData object into a CustomerData array
	{
		return json_decode(json_encode($this), true);
	}
	public function save()
	{
		$this->Customer->save();
		foreach($this->AddressList as $a)
		{
			$a->save();
		}
		foreach($this->AppointmentList as $ap)
		{
			$ap->save();
		}
		foreach($this->ServiceQuoteList as $s)
		{
			$s->save();
		}
	}
	//Private Methods
	private static function address_list_get($cust_id)
	{
		$list = [];
		
		$ids = Appointment::where('aptcust_id',$cust_id)->pluck('aptaddress_id');
	
		$list = Address::with('state','office','teamarea')->where('address_id',$ids)->get();
		return $list;
	}
	private static function appointment_list_get($cust_id)
	{
		$list = [];
		$ids = Appointment::where('aptcust_id',$cust_id)->pluck('appointment_id');
		$list = Appointment::where('appointment_id',$ids)->get();
		return $list;
	}	
	private static function servicequote_list_get($cust_id)
	{
		$list = [];
		$ids = Appointment::where('aptcust_id', $cust_id)->pluck('appointment_id');
		$list = ServiceQuote::where('qteappointment_id', $ids)->get();
		return $list;	
	}	
	private function SyncCustomerData($custID)
	{ 
		//$query = " SELECT EXISTS(SELECT 1/0 FROM customer WHERE cust_id='" . $custID . "') AS 'result';";
		
		return Customer::where('cust_id',$custID)->exists();	
	}
	
	//This function returns a list of CustomerData that has been updated since the office->sync_timestamp.
	//I think this is currently being handled by CustomerDataList object
	public static function update_list_get(Office $office)
	{
		
		$list = [];

		//capture the sync_timestamp as a DateTime object;
		$dt = Office::sync_timestamp_get($office->office_id);
		$fdt = ($dt->format('Y-m-d H:i:s'));
		
		//get a list of cust_id that were updated after the sync_timestamp.
		$ids = Customer::whereDate('updated_at','>', $dt->format('Y-m-d H:i:s'))->get()->pluck('cust_id');

		//capture each CustomerData object by cust_id, and add it to the return list.
		foreach($ids as $key => $value)
		{
			$cd = CustomerData::find($value);
			$list[] = $cd;
		}

		return $list;
	}
	//this returns a list of CustomerData objects based on any Customer table Key/Value property
	//I'm not sure where this function is being used	
	public static function where($key, $value)
	{
		$list = [];
		$ids = Customer::where($key, $value)->pluck('cust_id')->get();
		foreach($ids as $id)
		{
			$c = new CustomerData;
			$c->Customer = Customer::find($id);
			$c->AddressList = Self::address_list_get($id);
			$c->ServiceQuoteList = Self::servicequote_list_get($id);
			$c->AppointmentList = Self::appointment_list_get($id);
			$list[] = $c;
		}
		return $list;
	}

}