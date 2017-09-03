<?php
namespace App\Plugins\QuickbooksOnline\Models;

use App\Models\Address;

class QBAddress{
	//Properties
	public $Id;
	public $Line1;
	public $Line2;
	public $Line3;
	public $Line4;
	public $Line5;
	public $City;
	public $Country;
	public $CountrySubDivisionCode;
	public $PostalCode;
	public $Lat;
	public $Long;
	
	//Methods

	public static function create(Address $addrs)
	{
		$a = new QBAddress;
		$a->Line1 					= $addrs->address1;
		$a->Line2 					= $addrs->address2;
		$a->City 					= $addrs->city;
		$a->Country 				= "USA";
		$a->CountrySubDivisionCode 	= self::validate_state_id($addrs);
		$a->PostalCode 				= $addrs->zipcode;
		return $a;
	}

	public static function from_customerdata(CustomerData $cd)
	{
		$qb = new QBAddress;

		//This will handle one of multiple addresses in CustomerData object
		//iterate through each address in the customerdata
		foreach($cd->AddressList as $addrs)
		{
			//if the address is marked for QBsync AND is active then handle it
			if ($addrs->adrqbsync == 1 && $addrs->adrisactive == 1)
			{ 
				//if the address is marked as a billing addresss, use the billing address
				//NOTE: remove the empty check on BillAddr once billing address is removed from the Customer table and inculuded in address table
				
				if ($addrs->adrisbilling == 1 && empty($qb->Customer->BillAddr->Line1))
				{  
					$billAddrID = $addrs->address_id; //This will allow us to update the correct billing address record with the BatchItemResponse
					if(isset($addrs->adrqbid)){
						$qb->Customer->BillAddr->Id = $addrs->adrqbid;
					} 
					else 
					{ 
						unset($qb->Customer->BillAddr->Id); 
					}	
					$qb->Customer->BillAddr->Line1 					= $addrs->address1;
					$qb->Customer->BillAddr->Line2 					= $addrs->address2;
					$qb->Customer->BillAddr->City 					= $addrs->city;
					$qb->Customer->BillAddr->Country 				= "USA";
					$qb->Customer->BillAddr->CountrySubDivisionCode = self::validate_state_id($addrs);
					$qb->Customer->BillAddr->PostalCode 			= $addrs->zipcode;	
				} 
				else
				{ //else assign it to shipping	
					$shipAddrID = $addrs->address_id; //This will allow us to update the correct shipping address record with the BatchItemResponse
					if(isset($addrs->adrqbid))
					{
						$qb->Customer->ShipAddr->Id = $addrs->adrqbid;
					} 
					else 
					{ 
						unset($b->Customer->ShipAddr->Id); 
					}	
					$qb->Customer->ShipAddr->Line1 					= $addrs->address1;
					$qb->Customer->ShipAddr->Line2 					= $addrs->address2;
					$qb->Customer->ShipAddr->City 					= $addrs->city;
					$qb->Customer->ShipAddr->Country 				= "USA";
					$qb->Customer->ShipAddr->CountrySubDivisionCode = self::validate_state_id($addrs);
					$qb->Customer->ShipAddr->PostalCode 			= $addrs->zipcode;
				}
				//For now assign the billing address to shipping if it is not already populated for testing purposes; remove this later
				if (empty($qb->Customer->ShipAddr->Line1)) 
				{
					$shipAddrID = $addrs->address_id; //This will allow us to update the correct shipping address record with the BatchItemResponse
					if(isset($addrs->adrqbid))
					{
						$qb->Customer->ShipAddr->Id = $addrs->adrqbid;
					} 
					else 
					{ 
						unset($qb->Customer->ShipAddr->Id); 
					}	
					$qb->Customer->ShipAddr->Line1 					= $addrs->address1;
					$qb->Customer->ShipAddr->Line2 					= $addrs->address2;
					$qb->Customer->ShipAddr->City 					= $addrs->city;
					$qb->Customer->ShipAddr->Country 				= "USA";
					$qb->Customer->ShipAddr->CountrySubDivisionCode = self::validate_state_id($addrs);
					$qb->Customer->ShipAddr->PostalCode 			= $addrs->zipcode;
				}
			} 
			else 
			{ 
				//for now write the addess even if its not marked for QBsycn or is not marked active 
			    //but we need to write BlueCard logic to select one shipping and one billing address to be synced
				if ($addrs->adrisbilling == 1)
				{ 
					//if its marked as a billing addresss assign it to billing
					$billAddrID = $addrs->address_id; //This will allow us to update the correct billing address record with the BatchItemResponse
					if(isset($addrs->adrqbid))
					{
						$qb->Customer->BillAddr->Id = $addrs->adrqbid;
					} 
					else 
					{ 
						unset($qb->Customer->BillAddr->Id); 
					}	
					$qb->Customer->BillAddr->Line1 					= $addrs->address1;
					$qb->Customer->BillAddr->Line2 					= $addrs->address2;
					$qb->Customer->BillAddr->City 					= $addrs->city;
					$qb->Customer->BillAddr->Country 				= "USA";
					$qb->Customer->BillAddr->CountrySubDivisionCode = self::validate_state_id($addrs);
					$qb->Customer->BillAddr->PostalCode 			= $addrs->zipcode;	
				} 
				else 
				{ 
					//else assign it to shipping	
					$shipAddrID = $addrs->address_id; //This will allow us to update the correct shipping address record with the BatchItemResponse	
					if(isset($addrs->adrqbid))
					{
						$qb->Customer->ShipAddr->Id = $addrs->adrqbid;
					} 
					else 
					{ 
						unset($qb->Customer->BillAddr->Id); 
					}	
					$qb->Customer->ShipAddr->Line1 					= $addrs->address1;
					$qb->Customer->ShipAddr->Line2 					= $addrs->address2;
					$qb->Customer->ShipAddr->City 					= $addrs->city;
					$qb->Customer->ShipAddr->Country 				= "USA";
					$qb->Customer->ShipAddr->CountrySubDivisionCode = self::validate_state_id($addrs);
					$qb->Customer->ShipAddr->PostalCode 			= $addrs->zipcode;
				}	
				//For now assign the billing address to shipping if it is not already populated for testing purposes; remove this later
				if (empty($qb->Customer->ShipAddr->Line1)) 
				{
					$shipAddrID = $addrs->address_id; //This will allow us to update the correct shipping address record with the BatchItemResponse
					if(isset($addrs->adrqbid))
					{
						$qb->Customer->ShipAddr->Id = $addrs->adrqbid;
					} 
					else 
					{ 
						unset($qb->Customer->ShipAddr->Id); 
					}	
					$qb->Customer->ShipAddr->Line1 					= $addrs->address1;
					$qb->Customer->ShipAddr->Line2 					= $addrs->address2;
					$qb->Customer->ShipAddr->City 					= $addrs->city;
					$qb->Customer->ShipAddr->Country 				= "USA";
					$qb->Customer->ShipAddr->CountrySubDivisionCode = self::validateAdrStateID($addrs);
					$qb->Customer->ShipAddr->PostalCode 			= $addrs->zipcode;
				}
			}
		}

		return $qb;
	}
	public function fill(Array $array)
	{
		foreach($array as $key => $value)
		{
			$this->$key = $value;
		}
	}
	private function validate_state_id($state_id)
	{
		if($value != null)
		{
			$brv = State::find($state_id)->pluck('stabrv');
			return $brv;
		}
		return null;
	}


}
?>