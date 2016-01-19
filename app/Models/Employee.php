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
		
	public static function boot() 
	{
        parent::boot();
 
        Employee::observe(new EmployeeObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
