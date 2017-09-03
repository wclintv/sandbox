<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ReflectionClass;
use Schema;
use DB;
use App\Models\Address;
use App\Models\ApiResponse;
use App\Models\Application;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\AppointmentType;
use App\Models\CancelBy;
use App\Models\Customer;
use App\Models\CustomerSearchData;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\EmployeeStatus;
use App\Models\Frequency;
use App\Models\Housecode;
use App\Models\Keylock;
use App\Models\Office;
use App\Models\PaymentMethod;
use App\Models\Price;
use App\Models\PSchedule;
use App\Models\Rank;
use App\Models\Redfile;
use App\Models\ReferredBy;
use App\Models\SecurityPrivileges;
use App\Models\ServiceDay;
use App\Models\ServiceItem;
use App\Models\ServiceQuote;
use App\Models\ServiceTime;
use App\Models\State;
use App\Models\Suffix;
use App\Models\TeamArea;

class DatabaseController extends Controller
{
	//Methods
	public static function create()
	{
		//Build ALL tables and triggers & Load Secondary table data
		//NOTE: MUST build No Foreign Key tables BEFORE building Foreign Key tables and Primary Foreign Key Tables
		//NOTE: Foreign Key tables MUST be built in the order listed below to ensure relational integrity THEN
		//	Primary foreign key tables MUST be build in the order listed below to ensure relational integrity 
        //NOTE: BuildCustomerSearch table is included with BuildPrimarytables
		//NOTE: All tables are built with InnoDB engine EXCEPT the CustomerSearch table which is built
		//      with the MyISAM engine; this was done because GoDaddy shared hosting MySQL version does
		//      not currently support FULLTEXT searching on InnoDB tables
		
		if(Self::BuildNFKtables())
		{
			if(Self::BuildFKtables())
			{
				if(Self::BuildPrimaryTables())
				{
					return 1;
				} else {return 0;}
			} else {return 0;}
		} else {return 0;}
	}
	public static function delete()
	{
		/****SUMMARY****
		Drops all tables from database
		NOTE: Tables with foreign key constraints must be deleted in the reverse order of which they were 
		built in order not to violate parent/child relational integrity.  In the same vein, constrained 
		tables must be deleted before tables without foreign key constraints.
		NOTE: DropCustomerSearch table is included with DropPrimarytables
		****************/	

		if(Self::DropPrimarytables())
		{
			if(Self::DropFKTables())
			{
				if(Self::DropNFKtables())
				{
					return 1;
				} else {return 0;}
			} else {return 0;}	
		} else {return 0;}	
	}
	public static function load_defaults()
	{
		if(Self::LoadNFKtables())
		{
			if(Self::LoadFKtables())
			{
				return 1;
			}else {return 0;}
		}else {return 0;}
	}

	/////////////////////////////////////DROP TABLES//////////////////////////////////////////
	private static function DropPrimaryTables()
	{
		/*1*/		if(Schema::hasTable('customersearchdata'))	{Schema::drop('customersearchdata');}
		/*2*/		if(Schema::hasTable('servicequote'))		{Schema::drop('servicequote');}
		/*3*/		if(Schema::hasTable('appointment'))			{Schema::drop('appointment');}
		/*4*/		if(Schema::hasTable('address'))				{Schema::drop('address');}
		/*5*/		if(Schema::hasTable('customer'))			{Schema::drop('customer');}
					return true;
	}
	private static function DropFKTables()
	{
		/*3*/		if(Schema::hasTable('employee'))			{Schema::drop('employee');}
		/*2*/		if(Schema::hasTable('office'))				{Schema::drop('office');}
		/*1*/		if(Schema::hasTable('priceschedule'))		{Schema::drop('priceschedule');}
					return true;
	}
	private static function DropNFKtables()
	{
					if(Schema::hasTable('application'))			{Schema::drop('application');}
		/*1*/		if(Schema::hasTable('appointmentstatus'))	{Schema::drop('appointmentstatus');}
		/*2*/		if(Schema::hasTable('appointmenttype'))		{Schema::drop('appointmenttype');}
		/*3*/		if(Schema::hasTable('cancelby'))			{Schema::drop('cancelby');}
		/*4*/		if(Schema::hasTable('employeeposition'))	{Schema::drop('employeeposition');}
		/*5*/		if(Schema::hasTable('employeestatus'))		{Schema::drop('employeestatus');}
		/*6*/		if(Schema::hasTable('frequency'))			{Schema::drop('frequency');}
		/*7*/		if(Schema::hasTable('housecode'))			{Schema::drop('housecode');}
		/*8*/		if(Schema::hasTable('keylock'))				{Schema::drop('keylock');}
		/*9*/		if(Schema::hasTable('paymentmethod'))		{Schema::drop('paymentmethod');}
		/*10*/		if(Schema::hasTable('price'))				{Schema::drop('price');}
		/*11*/		if(Schema::hasTable('pschedule'))			{Schema::drop('pschedule');}
		/*12*/		if(Schema::hasTable('rank'))				{Schema::drop('rank');}
		/*13*/		if(Schema::hasTable('redfile'))				{Schema::drop('redfile');}
		/*14*/		if(Schema::hasTable('referredby'))			{Schema::drop('referredby');}
		/*15*/		if(Schema::hasTable('securityprivileges'))	{Schema::drop('securityprivileges');}
		/*16*/		if(Schema::hasTable('serviceday'))			{Schema::drop('serviceday');}
		/*17*/		if(Schema::hasTable('serviceitem'))			{Schema::drop('serviceitem');}
		/*18*/		if(Schema::hasTable('servicetime'))			{Schema::drop('servicetime');}
		/*19*/		if(Schema::hasTable('state'))				{Schema::drop('state');}
		/*20*/		if(Schema::hasTable('suffix'))				{Schema::drop('suffix');}
		/*21*/		if(Schema::hasTable('teamarea'))			{Schema::drop('teamarea');}
					if(Schema::hasTable('users'))				{Schema::drop('users');}
					return true;
	}
	/////////////////////////////////////BUILD TABLES//////////////////////////////////////////
	private static function BuildPrimaryTables()
	{
		/*1*/		if(!Schema::hasTable('customer'))			{Self::customer_build_table();}
		/*2*/		if(!Schema::hasTable('address'))			{Self::address_build_table();}
		/*3*/		if(!Schema::hasTable('appointment'))		{Self::appointment_build_table();}
		/*4*/		if(!Schema::hasTable('servicequote'))		{Self::servicequote_build_table();}
		/*5*/		if(!Schema::hasTable('customersearchdata'))	{Self::customersearchdata_build_table();}
					return true;
	}
	private static function BuildFKTables()
	{
		/*1*/		if(!Schema::hasTable('priceschedule'))		{Self::priceschedule_build_table();}
		/*2*/		if(!Schema::hasTable('office'))				{Self::office_build_table();}
		/*3*/		if(!Schema::hasTable('employee'))			{Self::employee_build_table();}
					return true;
	}
	private static function BuildNFKtables()
	{
					if(!Schema::hasTable('users'))				{Self::users_build_table();}
					if(!Schema::hasTable('application'))		{Self::application_build_table();}
		/*1*/		if(!Schema::hasTable('appointmentstatus'))	{Self::appointmentstatus_build_table();}
		/*2*/		if(!Schema::hasTable('appointmenttype'))	{Self::appointmenttype_build_table();}
		/*3*/		if(!Schema::hasTable('cancelby'))			{Self::cancelby_build_table();}
		/*4*/		if(!Schema::hasTable('employeeposition'))	{Self::employeeposition_build_table();}
		/*5*/		if(!Schema::hasTable('employeestatus'))		{Self::employeestatus_build_table();}
		/*6*/		if(!Schema::hasTable('frequency'))			{Self::frequency_build_table();}
		/*7*/		if(!Schema::hasTable('housecode'))			{Self::housecode_build_table();}
		/*8*/		if(!Schema::hasTable('keylock'))			{Self::keylock_build_table();}
		/*9*/		if(!Schema::hasTable('paymentmethod'))		{Self::paymentmethod_build_table();}
		/*10*/		if(!Schema::hasTable('price'))				{Self::price_build_table();}
		/*11*/		if(!Schema::hasTable('pschedule'))			{Self::pschedule_build_table();}
		/*12*/		if(!Schema::hasTable('rank'))				{Self::rank_build_table();}
		/*13*/		if(!Schema::hasTable('redfile'))			{Self::redfile_build_table();}
		/*14*/		if(!Schema::hasTable('referredby'))			{Self::referredby_build_table();}
		/*15*/		if(!Schema::hasTable('securityprivileges'))	{Self::securityprivileges_build_table();}
		/*16*/		if(!Schema::hasTable('serviceday'))			{Self::serviceday_build_table();}
		/*17*/		if(!Schema::hasTable('serviceitem'))		{Self::serviceitem_build_table();}
		/*18*/		if(!Schema::hasTable('servicetime'))		{Self::servicetime_build_table();}
		/*19*/		if(!Schema::hasTable('state'))				{Self::state_build_table();}
		/*20*/		if(!Schema::hasTable('suffix'))				{Self::suffix_build_table();}
		/*21*/		if(!Schema::hasTable('teamarea'))			{Self::teamarea_build_table();}
		 			return true;
	}
	private static function address_build_table()
	{
		Schema::Create('address', function($table)
		{
			$table->increments('address_id');
			$table->string('adrqbid')->nullable();
			$table->integer('adroffice_id')->nullable()->unsigned();
			$table->integer('adrteamarea_id')->nullable()->unsigned();
			$table->string('address1', 45)->nullable();
			$table->string('address2', 45)->nullable();
			$table->string('city', 45)->nullable();
			$table->integer('adrstate_id')->nullable()->unsigned();
			$table->string('zipcode', 12)->nullable();
			$table->integer('bed')->nullable();
			$table->decimal('bath', 2, 1)->nullable();
			$table->integer('sqft')->nullable();
			$table->tinyInteger('keylock')->nullable();
			$table->tinyInteger('adrqbsync')->default(0);
			$table->tinyInteger('adrisactive')->nullable();
			$table->tinyInteger('adrisbilling')->nullable();
			$table->text('directions')->nullable();
			$table->timestamps();

			$table->index('adrstate_id');
			$table->index('adroffice_id');
			$table->index('adrteamarea_id');
			$table->index('city');

			$table->foreign('adroffice_id')->references('office_id')->on('office');
			$table->foreign('adrstate_id')->references('state_id')->on('state');
			$table->foreign('adrteamarea_id')->references('teamarea_id')->on('teamarea');
			
			$table->engine = "InnoDB";
		});
		
		DB::unprepared(DB::raw(
			"
			/*This trigger updates the update_timestamp on the associated Office record each time the Address record is UPDATED*/
			CREATE TRIGGER updateoffice_addr BEFORE UPDATE ON address FOR EACH ROW
			UPDATE office SET update_timestamp = CURRENT_TIMESTAMP 
			WHERE office_id = NEW.adroffice_id;
			
			/*This trigger updates the update_timestamp on the associated Office record each time the Address record is CREATED*/
			CREATE TRIGGER createoffice_addr BEFORE INSERT ON address FOR EACH ROW
			UPDATE office SET update_timestamp = CURRENT_TIMESTAMP 
			WHERE office_id = NEW.adroffice_id;

			/*This trigger updates the update_timestamp on the associated Customer record each time the Address record is UPDATED*/
			CREATE TRIGGER updatecust_addr AFTER UPDATE ON address FOR EACH ROW
			UPDATE customer SET update_timestamp = CURRENT_TIMESTAMP   
			WHERE cust_id = (
			SELECT aptcust_id
			FROM appointment
			INNER JOIN address ON aptaddress_id = address_id
			WHERE address_id = NEW.address_id);
			
			/*This trigger updates the update_timestamp on the associated Customer record each time the Address record is CREATED*/
			CREATE TRIGGER createcust_addr AFTER INSERT ON address FOR EACH ROW
			UPDATE customer SET update_timestamp = CURRENT_TIMESTAMP 
			WHERE cust_id = (
			SELECT aptcust_id
			FROM appointment
			INNER JOIN address ON aptaddress_id = address_id
			WHERE address_id = NEW.address_id);
			"
			));
	}
	private static function application_build_table()
	{
		Schema::Create('application', function($table)
		{
			$table->increments('application_id');
			$table->text('terms_of_service')->nullable();
			$table->text('privacy_policy')->nullable();
			$table->text('use_agreement')->nullable();
			$table->timestamps();
		});
	}
	private static function appointment_build_table()
	{
		Schema::Create('appointment', function($table)
		{
			$table->increments('appointment_id');
			$table->integer('aptcust_id')->nullable()->unsigned();
			$table->integer('aptaddress_id')->nullable()->unsigned();
			$table->integer('apttype_id')->nullable()->unsigned();
			$table->integer('aptstatus_id')->nullable()->unsigned();
			$table->integer('estimator_id')->nullable()->unsigned();
			$table->integer('aptserviceday_id')->nullable()->unsigned();
			$table->string('serviceday',45)->nullable();
			$table->dateTime('aptstart_datetime')->nullable();
			$table->dateTime('aptend_datetime')->nullable();
			$table->integer('aptreferredby_id')->nullable()->unsigned();
			$table->integer('aptcancelby_id')->nullable()->unsigned();
			$table->integer('aptpaymentmethod_id')->nullable()->unsigned();
			$table->tinyInteger('billed')->nullable();
			$table->tinyInteger('aptqbsync')->default(0);
			$table->tinyInteger('aptisactive')->nullable();
			$table->timestamps();

			$table->index('aptcust_id');
			$table->index('aptaddress_id');
			$table->index('estimator_id');
			$table->index('aptserviceday_id');
			$table->index('apttype_id');
			$table->index('aptstatus_id');
			$table->index('aptreferredby_id');
			$table->index('aptcancelby_id');
			$table->index('aptpaymentmethod_id');

			$table->foreign('aptcust_id')->references('cust_id')->on('customer')->onDelete('cascade');
			$table->foreign('aptaddress_id')->references('address_id')->on('address')->onDelete('cascade');
			$table->foreign('estimator_id')->references('employee_id')->on('employee');
			$table->foreign('aptserviceday_id')->references('serviceday_id')->on('serviceday');
			$table->foreign('apttype_id')->references('appointmenttype_id')->on('appointmenttype');
			$table->foreign('aptstatus_id')->references('appointmentstatus_id')->on('appointmentstatus');
			$table->foreign('aptreferredby_id')->references('referredby_id')->on('referredby');
			$table->foreign('aptcancelby_id')->references('cancelby_id')->on('cancelby');
			$table->foreign('aptpaymentmethod_id')->references('paymentmethod_id')->on('paymentmethod');
			
			$table->engine = "InnoDB";
		});
		
		DB::unprepared(DB::raw(
			"
			/*This trigger updates the update_timestamp on the associated Office record each time the Appointment record is UPDATED*/
			CREATE TRIGGER updateoffice_appt BEFORE UPDATE ON appointment FOR EACH ROW
			UPDATE office SET update_timestamp = CURRENT_TIMESTAMP 
			WHERE office_id = (
			SELECT adroffice_id
			FROM address
			INNER JOIN appointment ON address_id = aptaddress_id
			WHERE appointment_id = NEW.appointment_id);
			
			/*This trigger updates the update_timestamp on the associated Office record each time the Appointment record is CREATED*/
			CREATE TRIGGER createoffice_appt BEFORE INSERT ON appointment FOR EACH ROW
			UPDATE office SET update_timestamp = CURRENT_TIMESTAMP 
			WHERE office_id = (
			SELECT adroffice_id
			FROM address
			INNER JOIN appointment ON address_id = aptaddress_id
			WHERE appointment_id = NEW.appointment_id);

			/*This trigger updates the update_timestamp on the associated Customer record each time the Appointment record is UPDATED*/
			CREATE TRIGGER updatecust_appt AFTER UPDATE ON appointment FOR EACH ROW
			UPDATE customer SET update_timestamp = CURRENT_TIMESTAMP   
			WHERE cust_id = (
			SELECT aptcust_id
			FROM appointment
			WHERE appointment_id = NEW.appointment_id);
			
			/*This trigger updates the update_timestamp on the associated Customer record each time the Appointment record is CREATED*/
			CREATE TRIGGER createcust_appt AFTER INSERT ON appointment FOR EACH ROW
			UPDATE customer SET update_timestamp = CURRENT_TIMESTAMP
			WHERE cust_id = (
			SELECT aptcust_id
			FROM appointment
			WHERE appointment_id = NEW.appointment_id);
			"
			));
		
	}
	private static function appointmentstatus_build_table()
	{
		Schema::Create('appointmentstatus', function($table)
		{
			$table->increments('appointmentstatus_id');
			$table->string('aptstatus')->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function appointmenttype_build_table()
	{
		Schema::Create('appointmenttype', function($table)
		{
			$table->increments('appointmenttype_id');
			$table->string('apttype', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function cancelby_build_table()
	{
		Schema::Create('cancelby', function($table)
		{
			$table->increments('cancelby_id');
			$table->string('cancelreason', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function customer_build_table()
	{
		Schema::Create('customer', function($table)
		{
			$table->increments('cust_id');
			$table->string('qbid',45)->nullable();
			$table->string('parentqbid',45)->nullable();
			$table->string('title',45)->nullable();		
			$table->string('firstname',45);
			$table->string('middlename',45)->nullable();
			$table->string('lastname',45);
			$table->string('suffix', 45)->nullable();
			$table->integer('custsuffix_id')->nullable()->unsigned();
			
			$table->string('phone',45)->nullable();
			$table->string('altphone',45)->nullable();
			$table->string('mobilephone',45)->nullable();
			$table->string('fax', 45)->nullable();	
			
			$table->string('company', 45)->nullable();	
			$table->string('email',45)->nullable();
			$table->string('website',100)->nullable();
			
			$table->string('billingfirstname',45)->nullable();
			$table->string('billinglastname',45)->nullable();
			$table->string('billingaddress1',45)->nullable();
			$table->string('billingaddress2',45)->nullable();
			$table->string('billingcity',45)->nullable();
			$table->integer('billingstate_id')->nullable()->unsigned();
			$table->string('billingzipcode',45)->nullable();
			$table->string('balancedue',45)->nullable();
			$table->string('resalenum',45)->nullable();
			$table->tinyInteger('redfile')->default(0);
			$table->tinyInteger('seperatebillingaddress')->nullable();
			
			$table->timestamp('update_timestamp')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
			$table->tinyInteger('qbcustisactive')->nullable();
			$table->string('qbeditsequence',45)->nullable();
			$table->string('qbsynctoken',45)->nullable();
			$table->dateTime('qbstartdate')->nullable();
			$table->dateTime('qbenddate')->nullable();
			$table->dateTime('qbtimemodified')->nullable();					
			$table->timestamps();

			$table->index('lastname');
			$table->index('phone');
			$table->index('custsuffix_id');
			$table->index('billingstate_id');
			$table->index('billingcity');

			$table->foreign('billingstate_id')->references('state_id')->on('state');
			$table->foreign('custsuffix_id')->references('suffix_id')->on('suffix');
			
			$table->engine = "InnoDB";
		});
		
		DB::unprepared(DB::raw(
			"
			/*This trigger updates the update_timestamp on the associated Office record each time the Customer record is UPDATED*/
			CREATE TRIGGER updateoffice_cust BEFORE UPDATE ON customer FOR EACH ROW
			UPDATE office SET update_timestamp = CURRENT_TIMESTAMP 
			WHERE office_id = (
			SELECT adroffice_id
			FROM address
			INNER JOIN appointment ON aptaddress_id = address_id
			INNER JOIN customer ON aptcust_id = cust_id
			WHERE cust_id = NEW.cust_id);
			
			/*This trigger updates the update_timestamp on the associated Office record each time the Customer record is CREATED*/
			CREATE TRIGGER createoffice_cust BEFORE INSERT ON customer FOR EACH ROW
			UPDATE office SET update_timestamp = CURRENT_TIMESTAMP
			WHERE office_id = (
			SELECT adroffice_id
			FROM address
			INNER JOIN appointment ON aptaddress_id = address_id
			INNER JOIN customer ON aptcust_id = cust_id
			WHERE cust_id = NEW.cust_id);
			"
			));
	}
	private static function customersearchdata_build_table()
	{
		//if(
		Schema::Create('customersearchdata', function($table)
		{
			$table->integer('cust_id');
			$table->integer('address_id');
			$table->integer('office_id');
			$table->string('firstname');
			$table->string('lastname');
			$table->string('phone')->nullable();
			$table->string('mobilephone')->nullable();
			$table->string('address1')->nullable();
			$table->timestamps();
		});
		//){return 1;}
	}
	private static function employee_build_table()
	{
		Schema::create('employee', function($table)
		{
			
			$table->increments('employee_id');
			$table->integer('empoffice_id')->nullable()->unsigned();
			$table->integer('empposition_id')->nullable()->unsigned();
			$table->integer('emprank_id')->nullable()->unsigned();
			$table->integer('empstatus_id')->nullable()->unsigned();
			$table->string('empfirstname');
			$table->string('emplastname');
			$table->string('empmiddleinitial');
			$table->timestamps();
			
			$table->index('empstatus_id');
			$table->index('empposition_id');
			$table->index('empoffice_id');
			$table->index('emprank_id');
			
			$table->foreign('empstatus_id')->references('employeestatus_id')->on('employeestatus');
			$table->foreign('empposition_id')->references('employeeposition_id')->on('employeeposition');
			$table->foreign('emprank_id')->references('rank_id')->on('rank');
			$table->foreign('empoffice_id')->references('office_id')->on('office');
			
			$table->engine = "InnoDB";
		});
	}
	private static function employeeposition_build_table()
	{
		Schema::Create('employeeposition', function($table)
		{
			$table->engine = "InnoDB";	
			
			$table->increments('employeeposition_id');
			$table->string('position', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function employeestatus_build_table()
	{
		Schema::Create('employeestatus', function($table)
		{
			$table->engine = "InnoDB";
			
			$table->increments('employeestatus_id');
			$table->string('empstatus', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function frequency_build_table()
	{
		Schema::Create('frequency', function($table)
		{
			$table->increments('frequency_id');
			$table->string('frequencytype', 45)->default('Will Call');
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function housecode_build_table()
	{
		Schema::Create('housecode', function($table)
		{
			$table->string('lettergrade_id', 10)->default('LUNCH');
			$table->smallInteger('twostaffmin');
			$table->smallInteger('threestaffmin');
			$table->smallInteger('displayorder');
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function keylock_build_table()
	{
		Schema::Create('keylock', function($table)
		{
			$table->increments('keylock_id');
			$table->string('status', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function office_build_table()
	{
		Schema::Create('office', function($table)
		{ 
			$table->increments('office_id');			
			$table->timestamp('backup_timestamp')->nullable();
			$table->string('oauth_token',1000)->nullable();
			$table->dateTime('oauth_token_created')->nullable();			
			$table->string('oauth_token_secret',1000)->nullable();					
			$table->string('officeaddress1',45)->nullable();
			$table->string('officeaddress2',45)->nullable();
			$table->string('officecity',45)->nullable();
			$table->string('officename',45);
			$table->integer('officestate_id')->nullable()->unsigned();
			$table->string('officezipcode',45)->nullable();
			$table->string('offprischedule',45)->nullable();
			$table->integer('qb_admin_user_id')->nullable()->unsigned();
			$table->string('qb_realmid')->nullable()->unique();
			$table->tinyInteger('qb_setup_complete')->default(0);	
			$table->timestamp('sync_timestamp')->default('1976-10-06 00:00:01'); 
			$table->timestamp('update_timestamp')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
			$table->timestamps();

			$table->index('offprischedule');
			$table->index('officestate_id');
			$table->index('qb_admin_user_id');

			//$table->foreign('offprischedule')->references('prischedule')->on('pschedule');
			$table->foreign('officestate_id')->references('state_id')->on('state');
			$table->foreign('qb_admin_user_id')->references('user_id')->on('users');
			
			$table->engine = "InnoDB";
		});
	}
	private static function paymentmethod_build_table()
	{
		Schema::Create('paymentmethod', function($table)
		{
			$table->increments('paymentmethod_id');
			$table->string('pymtqbid')->nullable();
			$table->string('paymentoption', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function price_build_table()
	{
		Schema::Create('price', function($table)
		{
			$table->increments('price_id');
			$table->string('pricequote', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function priceschedule_build_table()
	{
		Schema::Create('priceschedule', function($table)
		{
			$table->increments('priceschedule_id');
			$table->string('schedule', 10)->nullable();
			$table->string('prilettergrade_id', 10)->nullable();			
			$table->integer('pricequote_id')->nullable()->unsigned();
			$table->timestamps();

			//$table->index('schedule');
			//$table->index('prilettergrade_id');
			//$table->index('pricequote_id');
			
			//$table->foreign('schedule')->references('prischedule')->on('pschedule');
			//$table->foreign('prilettergrade_id')->references('lettergrade_id')->on('housecode');
			//$table->foreign('pricequote_id')->references('price_id')->on('price');

			$table->engine = "InnoDB";
		});
	}
	private static function pschedule_build_table()
	{
		Schema::Create('pschedule', function($table)
		{
			$table->string('prischedule', 10)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function rank_build_table()
	{
		Schema::Create('rank', function($table)
		{
			$table->engine = "InnoDB";
			
			$table->increments('rank_id');
			$table->string('rank', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function redfile_build_table()
	{
		Schema::Create('redfile', function($table)
		{
			$table->increments('redfile_id');
			$table->string('status', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function referredby_build_table()
	{
		Schema::Create('referredby', function($table)
		{
			$table->increments('referredby_id');
			$table->string('referredby', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function securityprivileges_build_table()
	{
		Schema::Create('securityprivileges', function($table)
		{
			$table->increments('securityprivilege_id');
			$table->string('privilege', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function serviceday_build_table()
	{
		Schema::Create('serviceday', function($table)
		{
			$table->increments('serviceday_id');
			$table->string('day', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function serviceitem_build_table()
	{
		Schema::Create('serviceitem', function($table)
		{
			$table->increments('serviceitem_id');
			$table->string('srvitem', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function servicequote_build_table()
	{
		Schema::Create('servicequote', function($table)
		{
			$table->increments('servicequote_id');
			$table->integer('qteappointment_id')->nullable()->unsigned();
			$table->integer('qteserviceitem_id')->nullable()->unsigned();
			$table->integer('qtefreq_id')->nullable()->unsigned();
			$table->integer('qtepriceschedule_id')->nullable()->unsigned();
			$table->integer('qteservicetime_id')->nullable()->unsigned();
			$table->string('fixedservicetime')->nullable();
			$table->date('firstservicedate')->nullable();
			$table->date('lastservicedate')->nullable();
			$table->text('notes')->nullable();
			$table->tinyInteger('qteqbsync')->default(0);
			$table->tinyInteger('qteisactive')->nullable();
			$table->tinyInteger('qteisarchive')->nullable();
			$table->timestamps();

			$table->index('qteappointment_id');
			$table->index('qtefreq_id');
			$table->index('qtepriceschedule_id');
			$table->index('qteservicetime_id');
			$table->index('qteserviceitem_id');

			$table->foreign('qteappointment_id')->references('appointment_id')->on('appointment');
			$table->foreign('qtefreq_id')->references('frequency_id')->on('frequency');
			$table->foreign('qtepriceschedule_id')->references('priceschedule_id')->on('priceschedule');
			$table->foreign('qteservicetime_id')->references('servicetime_id')->on('servicetime');
			$table->foreign('qteserviceitem_id')->references('serviceitem_id')->on('serviceitem');
			
			$table->engine = "InnoDB";
		});
		
		DB::unprepared(DB::raw(
			"
			/*This trigger updates the update_timestamp on the associated Office record each time the ServiceQuote record is UPDATED*/
			CREATE TRIGGER updateoffice_srvq BEFORE UPDATE ON servicequote FOR EACH ROW
			UPDATE office SET update_timestamp = CURRENT_TIMESTAMP 
			WHERE office_id = (
			SELECT adroffice_id
			FROM address
			INNER JOIN appointment ON address_id = aptaddress_id
			INNER JOIN servicequote ON appointment_id = qteappointment_id
			WHERE servicequote_id = NEW.servicequote_id);
			
			/*This trigger updates the update_timestamp on the associated Office record each time the ServiceQuote record is CREATED*/
			CREATE TRIGGER createoffice_srvq BEFORE INSERT ON servicequote FOR EACH ROW
			UPDATE office SET update_timestamp = CURRENT_TIMESTAMP 
			WHERE office_id = (
			SELECT adroffice_id
			FROM address
			INNER JOIN appointment ON address_id = aptaddress_id
			INNER JOIN servicequote ON appointment_id = qteappointment_id
			WHERE servicequote_id = NEW.servicequote_id);

			/*This trigger updates the update_timestamp on the associated Customer record each time the ServiceQuote record is UPDATED*/
			CREATE TRIGGER updatecust_srvq AFTER UPDATE ON servicequote FOR EACH ROW
			UPDATE customer SET update_timestamp = CURRENT_TIMESTAMP   
			WHERE cust_id = (
			SELECT aptcust_id
			FROM appointment
			INNER JOIN servicequote ON appointment_id = qteappointment_id
			WHERE servicequote_id = NEW.servicequote_id);
			
			/*This trigger updates the update_timestamp on the associated Customer record each time the ServiceQuote record is CREATED*/
			CREATE TRIGGER createcust_srvq AFTER INSERT ON servicequote FOR EACH ROW
			UPDATE customer SET update_timestamp = CURRENT_TIMESTAMP 
			WHERE cust_id = (
			SELECT aptcust_id
			FROM appointment
			INNER JOIN servicequote ON appointment_id = qteappointment_id
			WHERE servicequote_id = NEW.servicequote_id);
			"
			));
	}
	private static function servicetime_build_table()
	{
		Schema::Create('servicetime', function($table)
		{
			$table->increments('servicetime_id');
			$table->string('servicetimeoption', 45);
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function state_build_table()
	{
		Schema::Create('state', function($table)
		{
			$table->increments('state_id');
			$table->string('statename', 45)->nullable();
			$table->string('stabrv', 2)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	private static function suffix_build_table()
	{
		Schema::Create('suffix', function($table)
		{
			$table->increments('suffix_id');
			$table->string('suffix', 10)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});		
	}
	private static function teamarea_build_table()
	{
		Schema::Create('teamarea', function($table)
		{
			$table->increments('teamarea_id');
			$table->string('area', 3)->nullable();
			$table->string('color', 45)->nullable();
			$table->timestamps();
			
			$table->engine = "InnoDB";
		});
	}
	public static function users_build_table()
	{
		Schema::Create('users', function($table)
		{
			$table->increments('user_id');			
			$table->string('email', 100)->unique();
			$table->string('firstname', 45)->nullable();
			$table->string('lastname', 45)->nullable();
			$table->string('name', 45);			
			$table->string('openid_claimed_id',100)->unique()->nullable();
			$table->string('openid_identity', 100)->unique()->nullable();
			$table->string('openid_sig',1000)->nullable();
			$table->string('openid_assoc_handle',1000)->nullable();
			$table->string('password', 100);
			$table->tinyInteger('qb_is_admin')->default(0);
			$table->string('qb_oauth_request_secret',1000)->nullable();
			$table->string('qb_realmid', 1000)->nullable();						
			$table->datetime('terms_accepted')->nullable();
			$table->integer('user_privileges')->default(1);					

			$table->rememberToken();
			$table->timestamps();

			$table->engine = "InnoDB";

		});
		return 1;
	}

	//////////////////////////////////LOAD DEFAULT DATA///////////////////////////////////////
	private static function LoadNFKtables() //Need to add validation that all data was loaded before true is returned
	{
					Self::application_load_data();
		/*1*/		Self::appointmentstatus_load_data();
		/*2*/		Self::appointmenttype_load_data();
		/*3*/		Self::cancelby_load_data();
		/*4*/		Self::employeeposition_load_data();
		/*5*/		Self::employeestatus_load_data();
		/*6*/		Self::frequency_load_data();
		/*7*/		Self::housecode_load_data();
		/*8*/		Self::keylock_load_data();
		/*9*/		Self::paymentmethod_load_data();
		/*10*/		Self::price_load_data();
		/*11*/		Self::pschedule_load_data();
		/*12*/		Self::rank_load_data();
		/*13*/		Self::redfile_load_data();
		/*14*/		Self::referredby_load_data();
		/*15*/		Self::securityprivileges_load_data();
		/*16*/		Self::serviceday_load_data();
		/*17*/		Self::serviceitem_load_data();
		/*18*/		Self::servicetime_load_data();
		/*19*/		Self::state_load_data();
		/*20*/		Self::suffix_load_data();
		/*21*/		Self::teamarea_load_data();
		return true;
	}
	private static function LoadFKtables() //Need to add validation that all data was loaded before true is returned
	{
		/*1*/		Self::priceschedule_load_data();
		/*2*/		Self::office_load_data();
		/*3*/		Self::employee_load_data();
		return true;
	}
	private static function apimethods_load_data()
	{
		$values = 
		[
		'Address_Create',
		'Address_Get',
		'Address_Update',
		'Address_Delete',
		'AppointmentStatusList_Get',
		'AppointmentTypeList_Get',
		'Appointment_Create',
		'Appointment_Get',
		'Appointment_Update',
		'Appointment_Delete',
		'CancelByList_Get',
		'Customer_Create',	
		'Customer_Get',
		'Customer_Update',
		'Customer_Delete',			
		'CustomerDataList_Get',
		'CustomerData_Create',
		'CustomerData_Get',
		'CustomerData_Update',
		'CustomerData_Delete',
		'CustomerSearchList_Get',
		'Database_Create',
		'Database_Delete',
		'EmployeePositionList_Get',
		'EmployeeStatusList_Get',
		'FrequencyList_Get',
		'GlobalLists_Get',
		'HousecodeList_Get',
		'KeylockList_Get',
		'OfficeList_Get',
		'Office_Get',
		'Office_Create',
		'Office_Update',			
		'PaymentMethodList_Get',
		'PriceScheduleList_Get',
		'Price_Get',
		'Price_Create',
		'Price_Update',			
		'RankList_Get',
		'ReferredByList_Get',
		'SecurityPrivilegesList_Get',
		'ServiceDayList_Get',
		'ServiceItemList_Get',
		'ServiceQuote_Get',
		'ServiceQuote_Create',
		'ServiceQuote_Update',
		'RedFileList_Get',
		'ServiceTimeList_Get',
		'StateList_Get',
		'SuffixList_Get',
		'TeamAreaList_Get',
		'TimeStamp_Get',
		'Employee_Get',
		'Employee_Update',
		'EmployeeList_Create',
		'EmployeeList_Get',
		'EmployeeList_Update',
		'QuickBooks_Create',
		'QuickBooks_Get',
		'QuickBooks_Sync',
		'QuickBooks_Update',
		'QuickBooks_Load',
		'User_Create',
		'User_Get',
		'User_Update',
		'User_Validate',
		'User_Login',
		'UserList_Create',
		'UserList_Get',
		'UserList_Update',
		'UserList_Delete',
		'Employee_Delete',
		'Office_Delete',
		'Price_Delete',
		'RankList_Delete',
		'ServiceQuote_Delete',
		'User_Delete'		
		];
	}
	private static function application_load_data()
	{
		//testing text
		$a = new Application;
		$a->terms_of_service = 
"
Posted/Revised: May 1, 2017

TERMS OF SERVICE

PLEASE READ THESE TERMS OF SERVICE CAREFULLY. BY CLICKING “ACCEPTED AND AGREED TO,” CUSTOMER AGREES TO THESE TERMS AND CONDITIONS.

These Terms of Service constitute an agreement (this “Agreement”) by and between Western Service Systems Inc., a corporation whose principal place of business is 295 Gentry Way Suite 3, Reno NV 89502 (“Vendor”) and the individual, corporation, LLC, partnership, sole proprietorship, or other business entity executing this Agreement (“Customer”). This Agreement is effective as of the date Customer clicks “Accepted and Agreed To” (the “Effective Date”). Customer’s use of and Vendor’s provision of Vendor’s System (as defined below in Section 1.6) are governed by this Agreement.

EACH PARTY ACKNOWLEDGES THAT IT HAS READ THIS AGREEMENT, UNDERSTANDS IT, AND AGREES TO BE BOUND BY ITS TERMS, AND THAT THE PERSON SIGNING ON ITS BEHALF HAS BEEN AUTHORIZED TO DO SO. THE PERSON EXECUTING THIS AGREEMENT ON CUSTOMER’S BEHALF REPRESENTS THAT HE OR SHE HAS THE AUTHORITY TO BIND CUSTOMER TO THESE TERMS AND CONDITIONS.

1.  DEFINITIONS. The following capitalized terms will have the following meanings whenever used in this Agreement.

1.1.	“AUP” means Vendor’s acceptable use policy currently posted at www.snapdsk.com/use_agreement .

1.2.	“Customer Data” means data in electronic form input or collected through the System by or from 
         Customer, including without limitation by Customer’s Users.

1.3.	“Documentation” means Vendor's materials and/or manuals and instructions related to use of the System 
         and/or SaaS products, as the same may be amended, replaced and/or supplemented from time to time.

1.4.	“Order” means an order for a SaaS product and/or access to the System (or, in the case of a free or trial 
         period, a request to access the System), for the stated time period identified when the purchase was 
         made or access was requested, executed as follows: through any snapdskTM ordering documentation or 
         online sign-up or subscription flow that references this Agreement.

1.5.	“Privacy Policy” means Vendor’s privacy policy, currently posted at www.snapdsk.com/privacy_policy .

1.6.	“System” means Vendor’s specific proprietary software-as-a-service product of snapdskTM specified in Customer’s Order Form, including any related snapdskTM Code and Documentation.

1.7.	“SLA” means Vendor’s standard service level agreement, currently posted at 
         www.snapdsk.com/terms_of_service .

1.8.	“Term” is defined in Section 11.1 below.

1.9.	“User” means any individual who uses the System on Customer’s behalf or through Customer’s account or passwords, whether authorized or not.

2.  THE SYSTEM. 

2.1.	Use of the System. During the Term, Customer may access and use the System pursuant to: (a) the terms 
         of any outstanding Order, including such features and functions as the Order requires; and (b) Vendor’s 
         policies posted on its Website at www.snapdsk.com , as such policies may be updated from time to time.

2.2.	Service Levels. Vendor will provide the remedies listed in the SLA for any failure of the System listed in 
        the SLA. Such remedies are Customer’s sole remedy for any failure of the System, and Customer 
        recognizes and agrees that if the SLA does not list a remedy for a given failure, it has no remedy. Credits 
        issued pursuant to the SLA apply to outstanding or future invoices only and are forfeit upon termination of 
        this Agreement. Vendor is not required to issue refunds or to make payments against such credits under 
        any circumstances, including without limitation after termination of this Agreement.

2.3.	Documentation: Customer may reproduce and use the Documentation solely as necessary to support Users’ use of the System.

2.4.	System Revisions. Vendor may revise System features and functions or the SLA at any time, including 
        without limitation by removing such features and functions or reducing service levels. If any such revision 
        to the System materially reduces features or functionality provided pursuant to an Order, Customer may 
        within 30 days of notice of the revision terminate such Order, without cause, or terminate this Agreement 
        without cause if such Order is the only one outstanding. If any such revision to the SLA materially reduces 
        service levels provided pursuant to an outstanding Order, the revisions will not go into effect with respect 
        to such Order until the start of the Term beginning 45 or more days after Vendor posts the revision and so 
        informs Customer.

3.  SYSTEM FEES. Customer will pay Vendor the fee set forth in each Order (the “Subscription Fee”) for each Term. Vendor will not be required to refund the Subscription Fee under any circumstances.

4.  CUSTOMER DATA & PRIVACY. 

4.1.	Use of Customer Data. Unless it receives Customer’s prior written consent, Vendor: (a) will not access, 
        process, or otherwise use Customer Data other than as necessary to facilitate the System; and (b) will not 
        intentionally grant any third party access to Customer Data, including without limitation Vendor’s other 
       customers, except subcontractors that are subject to a reasonable nondisclosure agreement. 
       Notwithstanding the foregoing, Vendor may disclose Customer Data as required by applicable law or by 
       proper legal or governmental authority. Vendor will give Customer prompt notice of any such legal or 
       governmental demand and reasonably cooperate with Customer in any effort to seek a protective order or 
       otherwise to contest such required disclosure, at Customer’s expense.

4.2.	Privacy Policy. The Privacy Policy applies only to the System and does not apply to any third party website 
        or service linked to the System or recommended or referred to through the System or by Vendor’s staff.

4.3.	Risk of Exposure. Customer recognizes and agrees that hosting data online involves risks of unauthorized 
        disclosure or exposure and that, in accessing and using the System, Customer assumes such risks. 
        Vendor offers no representation, warranty, or guarantee that Customer Data will not be exposed or 
        disclosed through errors or the actions of third parties.

4.4.	Data Accuracy. Vendor will have no responsibility or liability for the accuracy of data uploaded to the 
        System by Customer, including without limitation Customer Data and any other data uploaded by Users.

4.5.	Data Deletion. Vendor may permanently erase Customer Data if Customer’s account is delinquent, 
        suspended, or terminated for 30 days or more.

4.6.	Excluded Data. Customer represents and warrants that Customer Data does not and will not include, and 
        Customer has not and will not upload or transmit to Vendor's computers or other media, any data 
        (“Excluded Data”) regulated pursuant to any law, rule, order or regulation of any governmental entity 
        having jurisdiction over such data (the “Excluded Data Laws”). CUSTOMER RECOGNIZES AND AGREES 
        THAT: (a) VENDOR HAS NO LIABILITY FOR ANY FAILURE TO PROVIDE PROTECTIONS SET FORTH IN THE 
        EXCLUDED DATA LAWS OR OTHERWISE TO PROTECT EXCLUDED DATA; AND (b) VENDOR’S SYSTEMS 
        ARE NOT INTENDED FOR MANAGEMENT OR PROTECTION OF EXCLUDED DATA AND MAY NOT 
        PROVIDE ADEQUATE OR LEGALLY REQUIRED SECURITY FOR EXCLUDED DATA.

4.7.	Aggregate & Anonymized Data. Notwithstanding the provisions above of this Article 4, Vendor may use, 
        reproduce, sell, publicize, or otherwise exploit Aggregate Data in any way, in its sole discretion. 
        (“Aggregate Data” refers to Customer Data with the following removed: personally identifiable information 
        and the names and addresses of Customer and any of its Users or customers.)

5.  CUSTOMER’S RESPONSIBILITIES & RESTRICTIONS.

5.1.	Acceptable Use. Customer will comply with the AUP. Customer will not: (a) use the System for service 
        bureau or time-sharing purposes or in any other way allow third parties to exploit the System; (b) provide 
        System passwords or other log-in information to any third party; (c) share non-public System features or 
        content with any third party; or (d) access the System in order to build a competitive product or service, to 
        build a product using similar ideas, features, functions or graphics of the System, or to copy any ideas, 
        features, functions or graphics of the System. In the event that it suspects any breach of the requirements 
        of this Section 5.1, including without limitation by Users, Vendor may suspend Customer’s access to the 
        System without advanced notice, in addition to such other remedies as Vendor may have. Neither this 
        Agreement nor the AUP requires that Vendor take any action against Customer or any User or other third 
        party for violating the AUP, this Section 5.1, or this Agreement, but Vendor is free to take any such action it 
        sees fit.

5.2.	Unauthorized Access. Customer will take reasonable steps to prevent unauthorized access to the System, 
        including without limitation by protecting its passwords and other log-in information. Customer will notify 
        Vendor immediately of any known or suspected unauthorized use of the System or breach of its security 
        and will use best efforts to stop said breach.

5.3.	Compliance with Laws. In its use of the System, Customer will comply with all applicable laws, including 
        without limitation laws governing the protection of personally identifiable information and other laws 
        applicable to the protection of Customer Data.

5.4.	Users & System Access. Customer is responsible and liable for: (a) Users’ use of the System, including 
        without limitation unauthorized User conduct and any User conduct that would violate the AUP or the 
        requirements of this Agreement applicable to Customer; and (b) any use of the System through 
        Customer’s account, whether authorized or unauthorized.

6.  IP & FEEDBACK. 

6.1.	IP Rights to the System. Vendor retains all right, title, and interest in and to the System, including without 
        limitation all software used to provide the System and all graphics, user interfaces, logos, and trademarks 
        reproduced through the System. This Agreement does not grant Customer any intellectual property 
        license or rights in or to the System or any of its components. Customer recognizes that the System and 
        its components are protected by copyright and other laws.

6.2.	Feedback. Vendor has not agreed to and does not agree to treat as confidential any Feedback (as defined 
        below) Customer or Users provide to Vendor, and nothing in this Agreement or in the parties’ dealings 
        arising out of or related to this Agreement will restrict Vendor’s right to use, profit from, disclose, publish, 
        keep secret, or otherwise exploit Feedback, without compensating or crediting Customer or the User in 
        question. Notwithstanding the provisions of Article 7 below, Feedback will not be considered Confidential 
        Information, provided information Customer transmits with Feedback or related to Feedback may be 
        considered Confidential Information. (“Feedback” refers to any suggestion or idea for improving or 
        otherwise modifying any of Vendor’s products or services.)

7.  CONFIDENTIAL INFORMATION. “Confidential Information” refers to the following items Vendor discloses to Customer: (a) any document Vendor marks “Confidential”; (b) any information Vendor orally designates as “Confidential” at the time of disclosure, provided Vendor confirms such designation in writing within ten (10) business days; (c) the Documentation and System, whether or not marked or designated confidential; and (d) any other nonpublic, sensitive information Customer should reasonably consider a trade secret or otherwise confidential. Notwithstanding the foregoing, Confidential Information does not include information that: (i) is in Customer’s possession at the time of disclosure; (ii) is independently developed by Customer without use of or reference to Confidential Information; (iii) becomes known publicly, before or after disclosure, other than as a result of Customer’s improper action or inaction; or (iv) is approved for release in writing by Customer. Customer is on notice that the Confidential Information may include Vendor’s valuable trade secrets.

7.1.	Nondisclosure. Customer will not use Confidential Information for any purpose other than to exercise its 
        rights and/or to perform under this Agreement (the “Purpose”). Customer: (a) will not disclose Confidential 
        Information to any employee or contractor of Customer unless such person needs access in order to 
        facilitate the Purpose and executes a nondisclosure agreement with Customer with terms no less 
        restrictive than those of this Article 7; and (b) will not disclose Confidential Information to any other third 
        party without Vendor’s prior written consent. Without limiting the generality of the foregoing, Customer 
        will protect Confidential Information with the same degree of care it uses to protect its own confidential 
        information of similar nature and importance, but with no less than reasonable care. Customer will 
        promptly notify Vendor of any misuse or misappropriation of Confidential Information that comes to 
        Customer’s attention. Notwithstanding the foregoing, Customer may disclose Confidential Information as 
        required by applicable law or by proper legal or governmental authority. Customer will give Vendor 
        prompt notice of any such legal or governmental demand and reasonably cooperate with Vendor in any 
        effort to seek a protective order or otherwise to contest such required disclosure, at Vendor’s expense.

7.2.	Injunction. Customer agrees that breach of this Article 7 would cause Vendor irreparable injury, for which 
        monetary damages would not provide adequate compensation, and that in addition to any other remedy, 
        Vendor will be entitled to injunctive relief against such breach or threatened breach, without proving 
        actual damage or posting a bond or other security.

7.3.	Termination & Return. With respect to each item of Confidential Information, the obligations of Section 7.1 
        above (Nondisclosure) will terminate ninety (90) days after the date of disclosure; provided that such 
        obligations related to Confidential Information constituting Vendor’s trade secrets will continue so long as 
        such information remains subject to trade secret protection pursuant to applicable law. Upon termination 
        of this Agreement, Customer will return all copies of Confidential Information to Vendor or certify, in 
        writing, the destruction thereof.

7.4.	Retention of Rights. This Agreement does not transfer ownership of Confidential Information or grant a 
        license thereto. Vendor will retain all right, title, and interest in and to all Confidential Information.

7.5.	Exception & Immunity. Pursuant to the Defend Trade Secrets Act of 2016, 18 USC Section 1833(b), 
        Recipient is on notice and acknowledges that, notwithstanding the foregoing or any other provision of this 
        Agreement:

            (a)	Immunity. An individual shall not be held criminally or civilly liable under any Federal or State 
                        trade secret law for the disclosure of a trade secret that- (A) is made- (i) in confidence to a 
                        Federal, State, or local government official, either directly or indirectly, or to an attorney; and (ii) 
                        solely for the purpose of reporting or investigating a suspected violation of law; or (B) is made in 
                        a complaint or other document filed in a lawsuit or other proceeding, if such filing is made under 
                        seal.

            (b)	Use of Trade Secret Information in Anti-Retaliation Lawsuit. An individual who files a lawsuit for 
                        retaliation by an employer for reporting a suspected violation of law may disclose the trade 
                        secret to the attorney of the individual and use the trade secret information in the court 
                        proceeding, if the individual- (A) files any document containing the trade secret under seal; and 
                        (B) does not disclose the trade secret, except pursuant to court order.

8. REPRESENTATIONS & WARRANTIES. 

8.1.	From Vendor. Vendor represents and warrants that it is the owner of the System and of each and every 
        component thereof, or the recipient of a valid license thereto, and that it has and will maintain the full 
        power and authority to grant the rights granted in this Agreement without the further consent of any third 
        party. Vendor’s representations and warranties in the preceding sentence do not apply to use of the 
        System in combination with hardware or software not provided by Vendor. In the event of a breach of the        
        warranty in this Section 8.1, Vendor, at its own expense, will promptly take the following actions: (a) secure 
        for Customer the right to continue using the System; (b) replace or modify the System to make it 
        noninfringing; or (c) terminate the infringing features of the Service and refund to Customer any prepaid 
        fees for such features, in proportion to the portion of the Term left after such termination. In conjunction 
        with Customer’s right to terminate for breach where applicable, the preceding sentence states Vendor’s 
        sole obligation and liability, and Customer’s sole remedy, for breach of the warranty in this Section 8.1 and 
        for potential or actual intellectual property infringement by the System.

8.2.	From Customer. Customer represents and warrants that: (a) it has the full right and authority to enter into, 
        execute, and perform its obligations under this Agreement and that no pending or threatened claim or 
        litigation known to it would have a material adverse impact on its ability to perform as required by this 
        Agreement; (b) it has accurately identified itself and it has not provided any inaccurate information about 
        itself to or through the System; and (c) it is a corporation, the sole proprietorship of an individual 18 years 
        or older, or another entity authorized to do business pursuant to applicable law.

8.3.	Warranty Disclaimers. Except to the extent set forth in the SLA and in Section 8.1 above, CUSTOMER 
        ACCEPTS THE SYSTEM “AS IS” AND AS AVAILABLE, WITH NO REPRESENTATION OR WARRANTY OF 
        ANY KIND, EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION IMPLIED WARRANTIES OF 
        MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NONINFRINGEMENT OF 
        INTELLECTUAL PROPERTY RIGHTS, OR ANY IMPLIED WARRANTY ARISING FROM STATUTE, COURSE 
        OF DEALING, COURSE OF PERFORMANCE, OR USAGE OF TRADE. WITHOUT LIMITING THE GENERALITY 
        OF THE FOREGOING: (a) VENDOR HAS NO OBLIGATION TO INDEMNIFY OR DEFEND CUSTOMER OR 
        USERS AGAINST CLAIMS RELATED TO INFRINGEMENT OF INTELLECTUAL PROPERTY; (b) VENDOR 
        DOES NOT REPRESENT OR WARRANT THAT THE SYSTEM WILL PERFORM WITHOUT INTERRUPTION 
        OR ERROR; AND (c) VENDOR DOES NOT REPRESENT OR WARRANT THAT THE SYSTEM IS SECURE 
        FROM HACKING OR OTHER UNAUTHORIZED INTRUSION OR THAT CUSTOMER DATA WILL REMAIN 
        PRIVATE OR SECURE. 

9.  INDEMNIFICATION. Customer will defend, indemnify, and hold harmless Vendor and the Vendor Associates (as defined below) against any “Indemnified Claim,” meaning any third party claim, suit, or proceeding arising out of or related to Customer's alleged or actual use of, misuse of, or failure to use the System, including without limitation: (a) claims by Users or by Customer's employees, as well as by Customer’s own customers; (b) claims related to unauthorized disclosure or exposure of personally identifiable information or other private information, including Customer Data; (c) claims related to infringement or violation of a copyright, trademark, trade secret, or privacy or confidentiality right by written material, images, logos or other content uploaded to the System through Customer’s account, including without limitation by Customer Data; and (d) claims that use of the System through Customer’s account harasses, defames, or defrauds a third party or violates the CAN-Spam Act of 2003 or any other law or restriction on electronic advertising. Indemnified Claims include, without limitation, claims arising out of or related to Vendor’s negligence. Customer’s obligations set forth in this Article 9 include retention and payment of attorneys and payment of court costs, as well as settlement at Customer’s expense and payment of judgments. Vendor will have the right, not to be exercised unreasonably, to reject any settlement or compromise that requires that it admit wrongdoing or liability or subjects it to any ongoing affirmative obligations. (The “Vendor Associates” are Vendor’s officers, directors, shareholders, parents, subsidiaries, agents, successors, and assigns.)

10.  LIMITATION OF LIABILITY.

10.1. Dollar Cap. VENDOR’S LIABILITY ARISING OUT OF OR RELATED TO THIS AGREEMENT WILL NOT 
        EXCEED FIFTY U.S. DOLLARS ($50 US).

10.2. Exclusion of Consequential Damages. IN NO EVENT WILL VENDOR BE LIABLE TO CUSTOMER FOR ANY 
         CONSEQUENTIAL, INDIRECT, SPECIAL, INCIDENTAL, OR PUNITIVE DAMAGES ARISING OUT OF OR 
         RELATED TO THIS AGREEMENT.

10.3. Clarifications & Disclaimers. THE LIABILITIES LIMITED BY THIS ARTICLE 10 APPLY: (a) TO LIABILITY FOR 
         NEGLIGENCE; (b) REGARDLESS OF THE FORM OF ACTION, WHETHER IN CONTRACT, TORT, STRICT 
         PRODUCT LIABILITY, OR OTHERWISE; (c) EVEN IF VENDOR IS ADVISED IN ADVANCE OF THE 
         POSSIBILITY OF THE DAMAGES IN QUESTION AND EVEN IF SUCH DAMAGES WERE FORESEEABLE; 
         AND (d) EVEN IF CUSTOMER’S REMEDIES FAIL OF THEIR ESSENTIAL PURPOSE. If applicable law limits 
         the application of the provisions of this Article 10, Vendor’s liability will be limited to the maximum extent 
         permissible. For the avoidance of doubt, Vendor’s liability limits and other rights set forth in this Article 10 
         apply likewise to Vendor’s affiliates, licensors, suppliers, advertisers, agents, sponsors, directors, officers, 
         employees, consultants, and other representatives.

11.  Term & Termination.

11.1.	Term. The term of this Agreement (the “Term”) will commence on the Effective Date and continue for the 
        period set forth in the Order or, if none, for ninety (90) days. Thereafter, the Term will renew for successive 
        three (3) periods, unless either party refuses such renewal by written notice thirty (30) or more days before 
        the renewal date.

11.2.	Termination for Cause. Either party may terminate this Agreement for the other’s material breach by 
        written notice. Such notice will specify in detail the nature of the breach and will be effective in 30 days, or 
        more if specified in the notice, unless the other party first cures the breach.

11.3.	Effects of Termination. Upon termination of this Agreement, Customer will cease all use of the System and 
        delete, destroy, or return all copies of the Documentation in its possession or control. The following 
        provisions will survive termination or expiration of this Agreement: (a) any obligation of Customer to pay 
        fees incurred before termination; (b) Articles and Sections 6 (IP & Feedback), 7 (Confidential Information), 
        8.3 (Warranty Disclaimers), 9 (Indemnification), and 10 (Limitation of Liability); and (c) any other provision of 
        this Agreement that must survive to fulfill its essential purpose.

12.MISCELLANEOUS.

12.1.	Independent Contractors. The parties are independent contractors and will so represent themselves in all 
        regards. Neither party is the agent of the other, and neither may make commitments on the other’s behalf.

12.2.	Notices. Vendor may send notices pursuant to this Agreement to Customer’s email contact points 
        provided by Customer, and such notices will be deemed received 24 hours after they are sent. Customer 
        may send notices pursuant to this Agreement to info@snapdsk.com, and such notices will be deemed 
        received 72 hours after they are sent.

12.3.	Force Majeure. No delay, failure, or default, other than a failure to pay fees when due, will constitute a 
        breach of this Agreement to the extent caused by acts of war, terrorism, hurricanes, earthquakes, other 
        acts of God or of nature, strikes or other labor disputes, riots or other acts of civil disorder, embargoes, or 
        other causes beyond the performing party’s reasonable control.

12.4.	Assignment & Successors. Customer may not assign this Agreement or any of its rights or obligations 
        hereunder without Vendor’s express written consent. Except to the extent forbidden in this Section 12.4, 
        this Agreement will be binding upon and inure to the benefit of the parties’ respective successors and 
        assigns.

12.5.	Severability. To the extent permitted by applicable law, the parties hereby waive any provision of law that 
        would render any clause of this Agreement invalid or otherwise unenforceable in any respect. In the event 
        that a provision of this Agreement is held to be invalid or otherwise unenforceable, such provision will be 
        interpreted to fulfill its intended purpose to the maximum extent permitted by applicable law, and the 
        remaining provisions of this Agreement will continue in full force and effect.

12.6. No Waiver. Neither party will be deemed to have waived any of its rights under this Agreement by lapse 
         of time or by any statement or representation other than by an authorized representative in an explicit 
        written waiver. No waiver of a breach of this Agreement will constitute a waiver of any other breach of this 
        Agreement.

12.7.	Choice of Law & Jurisdiction: This Agreement and all claims arising out of or related to this Agreement will 
        be governed solely by the internal laws of the State of Nevada, including without limitation applicable 
        federal law, without reference to: (a) any conflicts of law principle that would apply the substantive laws of 
        another jurisdiction to the parties’ rights or duties; (b) the 1980 United Nations Convention on Contracts for 
        the International Sale of Goods; or (c) other international laws. The parties consent to the personal and 
        exclusive jurisdiction of the federal and state courts of Washoe County, Nevada. This Section 12.7 governs 
        all claims arising out of or related to this Agreement, including without limitation tort claims.

12.8. Conflicts. In the event of any conflict between this Agreement and any Vendor policy posted online, 
         including without limitation the AUP or Privacy Policy, the terms of this Agreement will govern.

12.9. Construction. The parties agree that the terms of this Agreement result from negotiations between them. 
         This Agreement will not be construed in favor of or against either party by reason of authorship.

12.10. Technology Export. Customer will not: (a) permit any third party to access or use the System in violation 
          of any U.S. law or regulation; or (b) export any software provided by Vendor or otherwise remove it from 
          the United States except in compliance with all applicable U.S. laws and regulations. Without limiting the 
          generality of the foregoing, Customer will not permit any third party to access or use the System in, or 
          export such software to, a country subject to a United States embargo (as of the Effective Date, Cuba, 
          Iran, North Korea, Sudan, and Syria).

12.11. Entire Agreement. This Agreement sets forth the entire agreement of the parties and supersedes all prior 
          or contemporaneous writings, negotiations, and discussions with respect to its subject matter. Neither 
          party has relied upon any such prior or contemporaneous communications.

12.12. Amendment. Vendor may amend this Agreement from time to time by posting an amended version 
          at its Website and sending Customer written notice thereof. Such amendment will be deemed accepted 
          and become effective 30 days after such notice (the “Proposed Amendment Date”) unless Customer first 
          gives Vendor written notice of rejection of the amendment. In the event of such rejection, this Agreement 
          will continue under its original provisions, and the amendment will become effective at the start of 
          Customer’s next Term following the Proposed Amendment Date (unless Customer first terminates this 
          Agreement pursuant to Article 11, Term & Termination). Customer’s continued use of the Service 
          following the effective date of an amendment will confirm Customer’s consent thereto. This Agreement 
          may not be amended in any other way except through a written agreement by authorized 
          representatives of each party. Notwithstanding the foregoing provisions of this Section 12.12, Vendor may 
          revise the Privacy Policy and Acceptable Use Policy at any time by posting a new version of either at the 
          Website, and such new version will become effective on the date it is posted.
";
		$a->privacy_policy = 
"
Effective Date: May 1, 2017                                                     

PRIVACY POLICY

We collect certain information through our website, located at www.snapdsk.com (our “Website”), including through the ¬products and services provided at the Website. This page (this “Privacy Policy”) lays out our policies and procedures surrounding the collection and handling of any such information that identifies an individual user or that could be used to contact or locate him or her (“Personally Identifiable Information” or “PII”).

This Privacy Policy applies only to our Website and to the products and services provided through our Website. It does not apply to any third party site or service linked to our Website or recommended or referred by our Website, through our products or services, or by our staff. And it does not apply to any other website, product, or service operated by our company, or to any of our offline activities.

A. PII We Collect
We collect the following Personally Identifiable Information from users who buy our products or services: name, e-mail address, telephone number, address, and credit card number.

We also use “cookies” to collect certain information from all users, including Web visitors who don’t buy anything through our Website. A cookie is a string of data our system sends to your computer and then uses to identify your computer when you return to our Website. Cookies give us usage data, like how often you visit, where you go at the site, and what you do.

B. Our Use of PII
We use your Personally Identifiable Information to create your account, to communicate with you about products and services you’ve purchased, to offer you additional products and services, and to bill you. We also use that information to the extent necessary to enforce our Website terms of service and to prevent imminent harm to persons or property.

We use cookies so that our Website can remember you and ¬provide you with the information you’re most likely to need. For instance, when you return to our Website, cookies identify you and prompt the site to provide your username (not your password), so you can sign in more quickly. Cookies also allow our Website to remind you of your past purchases and to suggest similar products and services. Finally, we use information gained through cookies to compile statistical information about use of our Website, such as the time users spend at the site and the pages they visit most often. Those statistics do not include PII.

C. Protection of PII
We have taken appropriate security measures, consistent with modern information practices, to protect your personal information. These measures include, on our web sites and Internet-enabled technologies, administrative, technical, physical and procedural steps to protect your data from misuse, unauthorized access or disclosure, loss, alteration, or destruction. When you enter sensitive information (such as log in credentials) on our registration or order forms, we encrypt that information using secure socket layer technology (SSL). Unfortunately, even with these measures, we cannot guarantee the security of PII. By using our Website, you acknowledge and agree that we make no such guarantee, and that you use our Website at your own risk.

D. Contractor and Other Third Party Access to PII
We give certain independent contractors access to Personally Identifiable Information. Those contractors assist us with login verification and credit card processing. All those contractors are required to sign contracts in which they promise to protect PII using procedures reasonably similar to ours. (Users are not third party beneficiaries of those contracts.) We also may disclose PII to attorneys, collection agencies, or law enforcement authorities to address potential AUP violations, other contract violations, or illegal behavior. And we disclose any information demanded in a court order or otherwise required by law or to prevent imminent harm to persons or property. Finally, we may share PII in connection with a corporate transaction, like a merger or sale of our company, or a sale of all or substantially all of our assets or of the product or service line you received from us, or a bankruptcy.

As noted above, we compile Website usage statistics from data collected through cookies. We may publish those statistics or share them with third parties, but they don’t include PII.

Except as set forth in this Privacy Policy, we do not share PII with third parties.

E. Accessing and Correcting Your PII
You can access and change any Personally Identifiable Information we store through your “My Account” page.

F. Amendment of This Privacy Policy
We may change this Privacy Policy at any time by posting a new version on this page or on a successor page. The new version will become effective on the date it’s posted, which will be listed at the top of the page as the new Effective Date.                                                                     
";
		$a->use_agreement = "
Date Posted: May 1, 2017

ACCEPTABLE USE POLICY

A. Unacceptable Use
Vendor requires that all customers and other users of Vendor’s cloud-based service (the “Service”) conduct themselves with respect for others. In particular, observe the following rules in your use of the Service:

1)   Abusive Behavior: Do not harass, threaten, or defame any person or entity. Do not contact any person who has requested no further contact. Do not use ethnic or religious slurs against any person or group.

2)   Privacy: Do not violate the privacy rights of any person. Do not collect or disclose any personal address, social security number, or other personally identifiable information without each holder’s written permission. Do not cooperate in or facilitate identity theft.

3)   Intellectual Property: Do not infringe upon the copyrights, trademarks, trade secrets, or other intellectual property rights of any person or entity. Do not reproduce, publish, or disseminate software, audio recordings, video recordings, photographs, articles, or other works of authorship without the written permission of the copyright holder.

4)   Hacking, Viruses, & Network Attacks: Do not access any computer or communications system without authorization, including the computers used to provide the Service. Do not attempt to penetrate or disable any security system. Do not intentionally distribute a computer virus, launch a denial of service attack, or in any other way attempt to interfere with the functioning of any computer, communications system, or website. Do not attempt to access or otherwise interfere with the accounts of other users of the Service.

5)   Spam: Do not send bulk unsolicited e-mails (“Spam”) or sell or market any product or service advertised by or connected with Spam. Do not facilitate or cooperate in the dissemination of Spam in any way. Do not violate the CAN-Spam Act of 2003.

6)   Fraud: Do not issue fraudulent offers to sell or buy products, services, or investments. Do not mislead anyone about the details or nature of a commercial transaction. Do not commit fraud in any other way.

7)   Violations of Law: Do not violate any law.

B. Consequences of Violation
Violation of this Acceptable Use Policy (this “AUP”) may lead to suspension or termination of the user’s account or legal action. In addition, the user may be required to pay for the costs of investigation and remedial action related to AUP violations. Vendor reserves the right to take any other remedial action it sees fit.

C. Reporting Unacceptable Use
Vendor requests that anyone with information about a violation of this AUP report it via e-mail to the following address: info@snapdsk.com. Please provide the date and time (with time zone) of the violation and any identifying information regarding the violator, including e-mail or IP (Internet Protocol) address if available, as well as details of the violation.

D. Revision of AUP
Vendor may change this AUP at any time by posting a new ¬version on this page and sending the user written notice thereof. The new version will become effective on the date of such notice.
";
		$a->save();
	}
	private static function appointmentstatus_load_data()
	{
		$values = [ 
		'Scheduled',
		'Rescheduled',
		'Canceled',
		'No Show',
		'No Bid',
		'Re-Bid',
		'Call Back',
		'Completed',
		'Will Call',
		'OTO'
		]; 	

		foreach($values as $value)
		{
			$a = new AppointmentStatus;
			$a->aptstatus = $value;
			$a->save();
		}
	}
	private static function appointmenttype_load_data()
	{
		$values = ['Repair','Estimate','Service'];
		foreach($values as $value)
		{
			$a = new AppointmentType;
			$a->apttype = $value;
			$a->save();
		}
	}
	private static function cancelby_load_data()
	{
		$values = ['Quality','Financial','Moving','Scheduling','Idle Customer','Red File','Not Tracked'];

		foreach($values as $value)
		{
			$c = new CancelBy;
			$c->cancelreason = $value;
			$c->save();	
		}	
	}
	private static function employeeposition_load_data()
	{
		$values = ['Housekeeper','Estimator','Operations Manager','Administrative Manager','General Manager','Brochure Dropper','Brochure Captain','Quality Supervisor'];
		
		foreach($values as $value)
		{
			$e = new EmployeePosition;
			$e->position = $value;
			$e->save();
		}
	}
	private static function employeestatus_load_data()
	{
		$values = ['Active', 'Inactive'];
		foreach($values as $value)
		{
			$x = new EmployeeStatus;
			$x->empstatus = $value;
			$x->save();
		}
	}
	private static function frequency_load_data()
	{
		$values = ['Weekly','Bi-Weekly','Monthly','Will Call','OTO','Red File','Inactive','unknown'];
		foreach($values as $value)
		{
			$x = new Frequency;
			$x->frequencytype = $value;
			$x->save();
		}					
	}
	private static function housecode_load_data()
	{
		$data = [
		['lettergrade_id'=>'CC','twostaffmin'=>'50','threestaffmin'=>'30','displayorder'=>'1'],
		['lettergrade_id'=>'BB','twostaffmin'=>'60','threestaffmin'=>'40','displayorder'=>'2'],
		['lettergrade_id'=>'AA','twostaffmin'=>'70','threestaffmin'=>'50','displayorder'=>'3'],
		['lettergrade_id'=>'A','twostaffmin'=>'80','threestaffmin'=>'55','displayorder'=>'4'],
		['lettergrade_id'=>'B','twostaffmin'=>'90','threestaffmin'=>'60','displayorder'=>'5'],
		['lettergrade_id'=>'C','twostaffmin'=>'100','threestaffmin'=>'70','displayorder'=>'6'],
		['lettergrade_id'=>'D','twostaffmin'=>'110','threestaffmin'=>'75','displayorder'=>'7'],
		['lettergrade_id'=>'E','twostaffmin'=>'120','threestaffmin'=>'80','displayorder'=>'8'],
		['lettergrade_id'=>'E+','twostaffmin'=>'140','threestaffmin'=>'90','displayorder'=>'9'],
		['lettergrade_id'=>'F','twostaffmin'=>'160','threestaffmin'=>'110','displayorder'=>'10'],
		['lettergrade_id'=>'F+','twostaffmin'=>'200','threestaffmin'=>'140','displayorder'=>'11'],
		['lettergrade_id'=>'DD','twostaffmin'=>'220','threestaffmin'=>'150','displayorder'=>'12'],
		['lettergrade_id'=>'EE','twostaffmin'=>'240','threestaffmin'=>'160','displayorder'=>'13'],
		['lettergrade_id'=>'E+E+','twostaffmin'=>'280','threestaffmin'=>'180','displayorder'=>'14'],
		['lettergrade_id'=>'G','twostaffmin'=>'60','threestaffmin'=>'60','displayorder'=>'15'],
		['lettergrade_id'=>'LUNCH','twostaffmin'=>'30','threestaffmin'=>'30','displayorder'=>'16'],
		['lettergrade_id'=>'unknown','twostaffmin'=>'0','threestaffmin'=>'30','displayorder'=>'17'],
		];
		Housecode::insert($data);
	}
	private static function keylock_load_data()
	{
		$values = ['Yes','No'];
		foreach($values as $value)
		{
			$x = new Keylock;
			$x->status = $value;
			$x->save();
		}	
	}
	private static function paymentmethod_load_data()
	{
		//$values = ['Visa','MasterCard','Discover','American Express','Cash','Check','Gift Certificate','Billed Customer','unknown'];
		$values = ['unknown'];
		foreach($values as $value)
		{
			$x = new PaymentMethod;
			$x->paymentoption = $value;
			$x->save();
		}	
	}
	private static function price_load_data()
	{
		$values = ['0','49','54','59','64','69','74','78','79','84','88','89','94','99','109','119','126','129','139','149','159','168','169','176','179','199','209','218','219','220','238','249','258','278','279','298','318','338','358','378'];
		foreach($values as $value)
		{
			$x = new Price;
			$x->pricequote = $value;
			$x->save();
		}	
	}
	private static function pschedule_load_data()
	{
		$values = ['26','28','42','44','49'];
		foreach($values as $value)
		{
			$x = new PSchedule;
			$x->prischedule = $value;
			$x->save();
		}	
	}
	private static function rank_load_data()
	{
		$values = ['Apprentice','Housekeeper','Senior','Lead1','Lead2','Lead3','Master1','Master2','Master3'];
		foreach($values as $value)
		{
			$x = new Rank;
			$x->rank = $value;
			$x->save();
		}			
	}
	private static function redfile_load_data()
	{
		$values = ['Yes','No'];
		foreach($values as $value)
		{
			$x = new Redfile;
			$x->status = $value;
			$x->save();
		}			
	}
	private static function referredby_load_data()
	{
		$values = ['Brochure','EDDM','Vehicle','Client','Phone Book','Internet','Returning Client','Not Tracked','WillCall Postcard'];
		foreach($values as $value)
		{
			$x = new ReferredBy;
			$x->referredby = $value;
			$x->save();
		}	
	}
	private static function securityprivileges_load_data()
	{
		$values = ['Guest','Operator','Manager','Admin','Highlander Status'];
		foreach($values as $value)
		{
			$x = new SecurityPrivileges;
			$x->privilege = $value;
			$x->save();
		}	
	}
	private static function serviceday_load_data()
	{
		$values = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','unknown'];
		foreach($values as $value)
		{
			$x = new ServiceDay;
			$x->day = $value;
			$x->save();
		}	
	}
	private static function serviceitem_load_data()
	{
		$values = ['White Glove Service','Limited Clean','Value Select','Double Clean','Hourly','WG-VS Rotation','unknown'];
		foreach($values as $value)
		{
			$x = new ServiceItem;
			$x->srvitem = $value;
			$x->save();
		}	
	}
	private static function servicetime_load_data()
	{
		$values =['FlexAny','FlexAM','FlexPM','Fixed','unknown'];
		foreach($values as $value)
		{
			$x = new ServiceTime;
			$x->servicetimeoption = $value;
			$x->save();
		}	
	}
	private static function state_load_data()
	{
		$data = [
		['statename'=>'Alabama','stabrv'=>'AL'],
		['statename'=>'Alaska','stabrv'=>'AK'],
		['statename'=>'Arizona','stabrv'=>'AZ'],
		['statename'=>'Arkansas','stabrv'=>'AR'],
		['statename'=>'California','stabrv'=>'CA'],
		['statename'=>'Colorado','stabrv'=>'CO'],
		['statename'=>'Connecticut','stabrv'=>'CT'],
		['statename'=>'Delaware','stabrv'=>'DE'],
		['statename'=>'Florida','stabrv'=>'FL'],
		['statename'=>'Georgia','stabrv'=>'GA'],
		['statename'=>'Hawaii','stabrv'=>'HI'],
		['statename'=>'Idaho','stabrv'=>'ID'],
		['statename'=>'Illinois','stabrv'=>'IL'],
		['statename'=>'Indiana','stabrv'=>'IN'],
		['statename'=>'Iowa','stabrv'=>'IA'],
		['statename'=>'Kansas','stabrv'=>'KS'],
		['statename'=>'Kentucky','stabrv'=>'KY'],
		['statename'=>'Louisiana','stabrv'=>'LA'],
		['statename'=>'Maine','stabrv'=>'ME'],
		['statename'=>'Maryland','stabrv'=>'MD'],
		['statename'=>'Massachusetts','stabrv'=>'MA'],
		['statename'=>'Michigan','stabrv'=>'MI'],
		['statename'=>'Minnesota','stabrv'=>'MN'],
		['statename'=>'Mississippi','stabrv'=>'MS'],
		['statename'=>'Missouri','stabrv'=>'MO'],
		['statename'=>'Montana','stabrv'=>'MT'],
		['statename'=>'Nebraska','stabrv'=>'NE'],
		['statename'=>'Nevada','stabrv'=>'NV'],
		['statename'=>'New Hampshire','stabrv'=>'NH'],
		['statename'=>'New Jersey','stabrv'=>'NJ'],
		['statename'=>'New Mexico','stabrv'=>'NM'],
		['statename'=>'New York','stabrv'=>'NY'],
		['statename'=>'North Carolina','stabrv'=>'NC'],
		['statename'=>'North Dakota','stabrv'=>'ND'],
		['statename'=>'Ohio','stabrv'=>'OH'],
		['statename'=>'Oklahoma','stabrv'=>'OK'],
		['statename'=>'Oregon','stabrv'=>'OR'],
		['statename'=>'Pennsylvania','stabrv'=>'PA'],
		['statename'=>'Rhode Island','stabrv'=>'RI'],
		['statename'=>'South Carolina','stabrv'=>'SC'],
		['statename'=>'South Dakota','stabrv'=>'SD'],
		['statename'=>'Tennessee','stabrv'=>'TN'],
		['statename'=>'Texas','stabrv'=>'TX'],
		['statename'=>'Utah','stabrv'=>'UT'],
		['statename'=>'Vermont','stabrv'=>'VT'],
		['statename'=>'Virginia','stabrv'=>'VA'],
		['statename'=>'Washington','stabrv'=>'WA'],
		['statename'=>'West Virginia','stabrv'=>'WV'],
		['statename'=>'Wisconsin','stabrv'=>'WI'],
		['statename'=>'Wyoming','stabrv'=>'WY'],
		['statename'=>'American Samoa','stabrv'=>'AS'],
		['statename'=>'District of Columbia','stabrv'=>'DC'],
		['statename'=>'Federated States of Micronesia','stabrv'=>'FM'],
		['statename'=>'Guam','stabrv'=>'GU'],
		['statename'=>'Marshall Islands','stabrv'=>'MH'],
		['statename'=>'Northern Mariana Islands','stabrv'=>'MP'],
		['statename'=>'Palau','stabrv'=>'PW'],
		['statename'=>'Puerto Rico','stabrv'=>'PR'],
		['statename'=>'Virgin Islands','stabrv'=>'VI'],
		['statename'=>'Armed Forces Africa','stabrv'=>'AE'],
		['statename'=>'Armed Forces Americas','stabrv'=>'AA'],
		['statename'=>'Armed Forces Canada','stabrv'=>'AE'],
		['statename'=>'Armed Forces Europe','stabrv'=>'AE'],
		['statename'=>'Armed Forces Middle East','stabrv'=>'AE'],
		['statename'=>'Armed Forces Pacific','stabrv'=>'AP'],
		['statename'=>'unknown','stabrv'=>'UN']
		];
		State::insert($data);
	}
	private static function suffix_load_data()
	{
		$values = ['Mr.','Ms.','Mrs.','Dr.','Jr.','Sr.','III'];
		foreach($values as $value)
		{
			$x = new Suffix;
			$x->suffix = $value;
			$x->save();
		}									
	}
	private static function teamarea_load_data()
	{
		$data = [
		['area'=>'1','color'=>'Red'],
		['area'=>'2','color'=>'Blue'],
		['area'=>'3','color'=>'Green'],
		['area'=>'4','color'=>'Yellow'],
		['area'=>'5','color'=>'Brown'],
		['area'=>'6','color'=>'Black'],
		['area'=>'7','color'=>'Orange'],
		['area'=>'8','color'=>'Purple'],
		['area'=>'9','color'=>'Grey'],
		['area'=>'10','color'=>'Black'],
		['area'=>'11','color'=>'Black'],
		['area'=>'12','color'=>'Black'],
		['area'=>'0','color'=>'Unknown'],
		['area'=>'14','color'=>'Black'],
		['area'=>'15','color'=>'Black'],
		['area'=>'16','color'=>'Black'],
		['area'=>'17','color'=>'Black'],
		['area'=>'18','color'=>'Black'],
		['area'=>'19','color'=>'Black'],
		['area'=>'20','color'=>'Black'],
		['area'=>'21','color'=>'Black'],
		['area'=>'22','color'=>'Black'],
		['area'=>'23','color'=>'Black'],
		['area'=>'24','color'=>'Black'],
		['area'=>'25','color'=>'Black'],
		['area'=>'26','color'=>'Black'],
		['area'=>'27','color'=>'Black'],
		];
		TeamArea::insert($data);
	}
	private static function priceschedule_load_data() /*Need to load data in here*/
	{
		
	}
	private static function office_load_data()
	{
		$data = [
			['officename'=>'unknown', 'officeaddress1'=>'unknown', 'officecity'=>'unknown', 
			'officestate_id'=>State::where('statename','unknown')->pluck('state_id')->first(), 
			'officezipcode'=>'unknown', 'qb_realmid'=>null],
		];
		Office::insert($data);
	}
	private static function employee_load_data()
	{
		$data = [
		['empfirstname'=>'Connor', 'empmiddleinitial'=>'C', 'emplastname'=>'MacLeod', 
		'empoffice_id'=>Office::where('officename','ClintSandbox')->pluck('office_id')->first(), 
		'empstatus_id'=> EmployeeStatus::where('empstatus','Active')->pluck('employeestatus_id')->first(),
		'empposition_id'=>EmployeePosition::where('position','General Manager')->pluck('employeeposition_id')->first(), 
		'emprank_id'=>Rank::where('rank.rank','Master3')->pluck('rank_id')->first()], 
		['empfirstname'=>'Admin', 'empmiddleinitial'=>'A', 'emplastname'=>'Admin', 
		'empoffice_id'=>Office::where('officename','ClintSandbox')->pluck('office_id')->first(), 
		'empstatus_id'=>EmployeeStatus::where('empstatus','Active')->pluck('employeestatus_id')->first(), 
		'empposition_id'=>EmployeePosition::where('position','General Manager')->pluck('employeeposition_id')->first(),  
		'emprank_id'=>Rank::where('rank.rank','Master3')->pluck('rank_id')->first()], 
		];
		Employee::insert($data);
	}
}
