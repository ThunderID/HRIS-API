<?php

namespace App\Models;

use App\Models\Traits\HasEmployeeTrait;
use App\Models\Traits\HasQuotaWorkleaveTrait;
use App\Models\Traits\HasCurrentMaritalStatusTrait;

use App\Models\Observers\EmployeeObserver;

/** 
	* Inheritance Person Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Employee extends Person
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\hasMany\HasWorksTrait;
	use \App\Models\Traits\hasMany\HasPersonWorkleavesTrait;
	use \App\Models\Traits\hasMany\HasPersonSchedulesTrait;
	use \App\Models\Traits\hasMany\HasMaritalStatusesTrait;
	use \App\Models\Traits\hasMany\HasLogsTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */	
	use HasEmployeeTrait;
	use HasQuotaWorkleaveTrait;
	use HasCurrentMaritalStatusTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'organisation_id'				,
											'uniqid' 						,
											'username' 						,
											'name' 							,
											'prefix_title' 					,
											'suffix_title' 					,
											'place_of_birth' 				,
											'date_of_birth' 				,
											'gender' 						,
											'password'						,
											'avatar'						,
											'last_password_updated_at'		,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'organisation_id'				=> 'exists:organisations,id',
											'uniqid' 						=> 'required|max:255',
											'username' 						=> 'max:255',
											'name' 							=> 'required|max:255',
											'prefix_title' 					=> 'max:255',
											'suffix_title' 					=> 'max:255',
											'place_of_birth' 				=> 'required|max:255',
											'date_of_birth' 				=> 'required|date_format:"Y-m-d"',
											'gender' 						=> 'required|in:female,male',
											'password'						=> 'max:255',
											'last_password_updated_at'		=> 'date_format:"Y-m-d H:i:s"|before:tomorrow',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/

	/**
	 * boot
	 * observing model
	 *
	 */			
	public static function boot() 
	{
        parent::boot();
 
        Employee::observe(new EmployeeObserver());
    }

	/**
	 * auto generate nik
	 *
	 * 1. get organisationcode
	 * 2. get join date
	 * 3. get last nik order number
	 * @param model of employee
	 * @return $nik
	 */			
	public function generateNIK($employee) 
	{
		//1. get organisationcode
		$code 			= $employee->organisation->code;

		//2. get join date
		$work 			= Work::personid($employee)->notchartid(0)->orderby('start', 'desc')->first();
		
		if($work)
		{
			$join_year 	= $work->start;
		}
		else
		{
			$join_year 	= Carbon::now();
		}

		$nik 			= $code.$join_year->format('y').'.';

		//3. get last nik order number
		$last_nik 		= Employee::selectraw('max(uniqid)')->where('uniqid', 'like', $nik.'%')->organisationid($employee->organisation_id)->first();

		if($last_nik)
		{
			$number		= 1 + (int)substr($last_nik['uniqid'],6);
		}
		else
		{
			$number 	= 1;
		}

		return $nik . str_pad($number,3,"0",STR_PAD_LEFT);
    }

	/**
	 * auto generate username
	 *
	 * 1. get organisationcode
	 * 2. get firstname
	 * @param model of employee
	 * @return $nik
	 */			
	public function generateUsername($employee) 
	{
		//1. get organisationcode
		$code 			= $employee->organisation->code;

		//2. get firstname
		$original		= explode(' ', strtolower($employee->name));
		$firstname		= $original[0];
		$countog		= count($original)-1;

		foreach ($original as $keyx => $valuex) 
		{
			if(is_array($valuex) || $valuex!='')
			{
				$countog 				= $keyx;
			}
		}

		$idxuname						= 0;
		
		do
		{
			$uname						= Employee::username($modify.'.'.$code)->first();

			if($uname)
			{
				if(isset($original[$countog]))
				{
					$modify 			= $modify.$original[$countog][$idxuname];
				}
				else
				{
					$modify 			= $modify.$modify;
				}

				$idxuname++;
			}
		}
		while($uname);

		return $modify.'.'.$code;
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
