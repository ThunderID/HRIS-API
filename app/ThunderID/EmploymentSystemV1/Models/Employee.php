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
	/**
	 * Relationship Traits.
	 *
	 */
	// use \App\ThunderID\EmploymentSystemV1\Models\Traits\HasMany\WorksTrait;
	use \App\ThunderID\PersonSystemV1\Models\Traits\HasMany\RelativesTrait;
	use \App\ThunderID\PersonSystemV1\Models\Traits\HasMany\MaritalStatusesTrait;
	use \App\ThunderID\PersonSystemV1\Models\Traits\HasMany\PersonDocumentsTrait;
	
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */	
	use HasEmployeeTrait;

	/**
	 * Global traits used as query builder (plugged scope).
	 *
	 */	
	use \App\ThunderID\EmploymentSystemV1\Models\Traits\GlobalTrait\HasOrganisationTrait;
	use \App\ThunderID\EmploymentSystemV1\Models\Traits\GlobalTrait\HasWorkTrait;
	
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
