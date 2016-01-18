<?php

namespace App\Models;

use App\Models\Traits\HasEmployeeTrait;
use App\Models\Traits\HasQuotaWorkleaveTrait;

// use App\Models\Observers\EmployeeObserver;

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
 	use \App\Models\Traits\hasMany\HasProcessLogsTrait;
	use \App\Models\Traits\hasMany\HasPersonWorkleavesTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */	
	use HasEmployeeTrait;
	// use HasQuotaWorkleaveTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        // Employee::observe(new EmployeeObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
