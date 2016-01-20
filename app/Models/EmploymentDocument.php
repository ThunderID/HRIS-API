<?php

namespace App\Models;

use App\Models\Traits\HasRequiredTrait;

/** 
	* Inheritance PersonDocument Model
	* For every inheritance model, allowed to have only $required, fillable, rules, and available function
*/
class EmploymentDocument extends PersonDocument
{
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasRequiredTrait;

	public $required 				= false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'document_id'					,
											'person_id'						,
										];
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'document_id'					=> 'max:255',
											'person_id'						=> 'date_format:"Y-m-d H:i:s"',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/**
	 * boot
	 *
	 */
	public static function boot() 
	{
        parent::boot();
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}