<?php

namespace App\Models;

use App\Models\Observers\ContactObserver;

/** 
	* Inheritance Contact Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class PersonContact extends Contact
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasPersonTrait;

	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */
	public $type_field				=	'person_type';

	public $type					=	['App\Models\Person', 'App\Models\Employee'];

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
											'item'							=> 'max:255',
											'person_type'					=> 'max:255|in:App\Models\Person,App\Models\Employee',
											'is_default'					=> 'boolean',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();

        PersonContact::observe(new ContactObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
