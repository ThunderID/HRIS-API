<?php

namespace App\Models;

use App\Models\Observers\GivenWorkleaveObserver;

/** 
	* Inheritance Person Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class GivenWorkleave extends PersonWorkleave
{
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */	
	use HasGivenWorkleaveTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'workleave_id'			,
											'person_id'				,
											'work_id' 				,
											'created_by' 			,
											'name' 					,
											'start' 				,
											'end' 					,
											'quota' 				,
											'status' 				,
											'notes'					,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'person_id'				=> 'exists:persons,id',
											'work_id' 				=> 'exists:works,id',
											'created_by' 			=> 'exists:persons,id',
											'name' 					=> 'max:255',
											'start' 				=> 'date_format:"Y-m-d H:i:s"',
											'end' 					=> 'date_format:"Y-m-d H:i:s"',
											'quota' 				=> 'numeric',
											'status' 				=> 'in:CN,CI',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/

	/**
	 * boot
	 * observing model
	 *
	 */			
	public static function boot() 
	{
        parent::boot();
 
        GivenWorkleave::observe(new GivenWorkleaveObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

}
