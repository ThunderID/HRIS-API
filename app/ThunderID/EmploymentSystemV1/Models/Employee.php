<?php

namespace App\ThunderID\EmploymentSystemV1\Models;

use App\ThunderID\EmploymentSystemV1\Models\Traits\GlobalTrait\HasEmployeeTrait;
use App\ThunderID\EmploymentSystemV1\Models\Observers\EmployeeObserver;
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
	use \App\ThunderID\EmploymentSystemV1\Models\Traits\HasMany\WorksTrait;
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
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
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
											'username' 						=> 'max:255',
											'name' 							=> 'max:255',
											'prefix_title' 					=> 'max:255',
											'suffix_title' 					=> 'max:255',
											'place_of_birth' 				=> 'max:255',
											'date_of_birth' 				=> 'date_format:"Y-m-d H:i:s"',
											'gender' 						=> 'in:female,male',
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
	 *
	 */			
	public static function boot() 
	{
        parent::boot();

        Employee::observe(new EmployeeObserver());
    }


	/**
	 * scope to get condition where username
	 *
	 * @param string or array of username
	 **/
	public function scopeUsername($query, $variable)
	{
		return 	$query->where($query->getModel()->table.'.username', $variable);
	}
}
