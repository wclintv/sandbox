<?php
namespace App\Plugins\QuickbooksOnline\Models;

use Auth;
use App\Plugins\QuickbooksOnline\Helpers\DateTimeConverter;
use App\Plugins\QuickbooksOnline\QuickbooksOnline;

use App\Models\PaymentMethod;

class QBPaymentMethod
{
	private static $startposition;
	private static $count;
	private static $setsize;
	private static $sets;
	
	//Properties
	public $Name;
	public $Active;
	public $Type;
	public $domain;
	public $sparse;
	public $Id;
	public $SyncToken;
	public $MetaData = array();
	
	public $value;
	
	public static function all()
	{
		//QuickBooks will only allow a maximum of 1000 records returned per query response so we will have to get all records through Pagination
		Self::trackers_reset();
		//Call QBs to find out how many total records are in the customer table
		Self::$count = Self::count();
		//this will divide the count into sets rounded up...NOTE: we could raise this as high as sets of 1000
		Self::$sets = ceil(Self::$count/Self::$setsize);
		
		//iterate through each set compiling the customers into a master list.
		$qbPaymentMethodList = [];
		for($i = 0; $i < Self::$sets; $i++)
		{
			//get a set list from quickbooks
			$list = Self::get_by_startposition(Self::$startposition);

			//Process each set list returned from quickbooks, and add it to the return list.
			foreach($list as $qbPaymentMethod)
			{
				$c = new QBPaymentMethod;
				$c->fill($qbPaymentMethod);
				$qbPaymentMethodList[] = $c;
			}
			Self::$startposition += Self::$setsize;
		}
		//returns a list of QBCustomer
		return $qbPaymentMethodList;		
	}
	public static function count()
	{
		$query = "SELECT COUNT(*) FROM PaymentMethod";
		$array = QuickbooksOnline::get($query);
		return $array['QueryResponse']['totalCount'];
	}
	public static function find($id) //Get function
	{
		$query = "SELECT * FROM PaymentMethod WHERE Id='" . $id . "'";
		$array = QuickbooksOnline::get($query);
		return $array['QueryResponse']['PaymentMethod'][0];
	}
	public static function get_update_count($timestamp)
	{
		//Call QBs to find out how many customer records have been updated since last sync
		$query = "SELECT COUNT(*) FROM PaymentMethod WHERE MetaData.LastUpdatedTime >= '" . $timestamp . "'";
		$array = QuickbooksOnline::get($query);
		return $array['QueryResponse']['totalCount'];
	}
	public static function get_update_list_from_quickbooks($timestamp)
	{
		Self::trackers_reset();
		Self::$count = Self::get_update_count($timestamp);
		Self::$sets = ceil(Self::$count/Self::$setsize);

		$paymentmethods = []; //declares $customer as an array
		for($i = 0; $i < Self::$sets; $i++)
		{
			$list = Self::get_by_startposition_timestamp(Self::$startposition,$timestamp);
			foreach($list as $paymentmethod)
			{
				$paymentmethods[] = $paymentmethod;
			}
			Self::$startposition + Self::$setsize;
		}
		return $paymentmethods;
	}
	public function to_paymentmethod()
	{
		$pm = new PaymentMethod;
		$pm->pymtqbid = $this->Id;
		$pm->paymentoption = $this->Name;
		
		return $pm;
	}
	public function toArray()
	{
		return json_decode($this, true);
	}
	public static function trackers_reset()
	{
		Self::$startposition = 1;
		Self::$count = null;
		Self::$setsize = 10; //max 1000
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
	public function fill(Array $array)
	{
		foreach($array as $key => $value)
		{
			$this->$key = $value;
		}
	}
	/////////////////////////////////*QuickBooks Database Calls*////////////////////////////////
	private static function get_by_startposition($start)
	{
		$query = "SELECT * FROM PaymentMethod STARTPOSITION " . $start . " MAXRESULTS " . Self::$setsize;		
		$array = QuickbooksOnline::get($query);
		return $array['QueryResponse']['PaymentMethod'];		
	}
	private static function get_by_startposition_timestamp($start, $timestamp)
	{
		$query = "SELECT * FROM PaymentMethod WHERE MetaData.LastUpdatedTime >= '" . $timestamp . "' STARTPOSITION " . $start . " MAXRESULTS " . Self::$setsize;
		$array = QuickbooksOnline::get($query);
		return $array['QueryResponse']['PaymentMethod'];
	}
}
?>