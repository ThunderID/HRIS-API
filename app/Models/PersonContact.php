<?php

namespace App\Models;

use App\Models\Traits\HasTypeTrait;

/** 
	* Inheritance Contact Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class PersonContact extends Contact
{
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasTypeTrait;

	public $type 						= false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'person_id'						,
											'item'							,
											'value'							,
											'person_type'					,
											'is_default'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'person_id'						=> 'exists:persons,id',
											'item'							=> 'required|max:255',
											'value'							=> 'required',
											'person_type'					=> 'required|max:255',
											'is_default'					=> 'boolean',
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
