<?php

namespace App\Models;

use App\Models\Observers\ContactObserver;

/** 
	* Inheritance Contact Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class BranchContact extends Contact
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasBranchTrait;

	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */
	public $type_field				=	'branch_type';

	public $type					=	'App\Models\Branch';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'branch_id'						,
											'item'							,
											'value'							,
											'branch_type'					,
											'is_default'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'branch_id'						=> 'exists:branches,id',
											'branch_type'					=> 'max:255|in:App\Models\Branch',
											'item'							=> 'max:255',
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
        
        BranchContact::observe(new ContactObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
