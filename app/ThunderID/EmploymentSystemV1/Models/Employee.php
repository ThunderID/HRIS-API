<?php

namespace App\ThunderID\EmploymentSystemV1\Models;

use App\ThunderID\EmploymentSystemV1\Models\Traits\GlobalTrait\HasEmployeeTrait;
use App\ThunderID\PersonSystemV1\Models\Person;
// use App\Models\Traits\HasQuotaWorkleaveTrait;

/** 
* Inheritance Person Model
* For every inheritance model
* @author cmooy
*/
class Employee extends Person
{
	const custom_prefix 		= 'hrps_';

	/**
	 * Relationship Traits.
	 *
	 */
	// use \App\ThunderID\EmploymentSystemV1\Models\Traits\HasMany\WorksTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */	
	use HasEmployeeTrait;
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/

	/**
	 * boot
	 *
	 */			
	public static function boot() 
	{
        parent::boot();
    }
}
