<?php

namespace App\Models;

use App\Models\Observers\TakenWorkleaveObserver;

/** 
	* Inheritance Person Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class TakenWorkleave extends PersonWorkleave
{
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */	
	use HasTakenWorkleaveTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
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
											'status' 				=> 'in:CB,CN,CI,OFFER,CONFIRMED',
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
 
        TakenWorkleave::observe(new TakenWorkleaveObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

}
