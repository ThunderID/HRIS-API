<?php

namespace App\Models;

// use App\Models\Observers\WorkExperienceObserver;

/**
 * Used for WorkExperience Models
 * 
 * @author cmooy
 */
class WorkExperience extends Work
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */
	public $type_field				=	'status';

	public $type					=	'previous';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'person_id'					,
											'position' 					,
											'organisation' 				,
											'status' 					,
											'start' 					,
											'end' 						,
											'reason_end_job' 			,
											'is_absence' 				,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'status' 					=> 'in:previous',
											'start' 					=> 'date_format:"Y-m-d"',
											'end' 						=> 'date_format:"Y-m-d"',
											'position' 					=> 'max:255',
											'organisation' 				=> 'max:255',
											'is_absence' 				=> 'boolean',
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
 
        // WorkExperience::observe(new WorkExperienceObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
