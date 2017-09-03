<?php
namespace App\Plugins\QuickbooksOnline\Helpers;
use DateTime;
class DateTimeConverter
{
	public static function to_datetime_from_quickbooks($qbTimestamp)
	{
		$dt = new DateTime($qbTimestamp);
		return $dt->format('Y-m-d');
	}


	public static function to_datetime_from_mysql($myTimestamp)
	{
		$dt = new DateTime($myTimestamp);
		return $dt->format('Y-m-d');
	}

	public static function datetime_to_quickbooks_timestamp(DateTime $dt)
	{
		//'u' is the variable that holds microseconds.  
		//NOTE:  Quickbooks might accept microseconds, making this step unnecessary.
		$micro = $dt->format('u');
		//divide it by 1000 to get milliseconds
		$milli = $micro/1000;
		//'P' is the UTC offset...capture it.
		$offset = $dt->format('P');
		//Construct the final format.

		return $dt->format("Y-m-d\TH:i:s.") . $milli . $offset;
	}
}




?>