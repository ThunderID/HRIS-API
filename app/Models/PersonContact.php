<?php

namespace App\Models;

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

	public $type					=	'App\Models\Person';

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
