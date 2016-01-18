<?php

namespace App\Models;

// use App\Models\Observers\CareerObserver;

/**
 * Used for Career Models
 * 
 * @author cmooy
 */
class Career extends Work
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */
	public $type_field				=	'status';

	public $type					=	['contract','probation','internship','permanent','others','admin'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'calendar_id' 				,
											'chart_id' 					,
											'grade' 					,
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
											'calendar_id'				=> 'exists:tmp_calendars,id',
											'chart_id'					=> 'required',
											'grade' 					=> 'numeric',
											'status' 					=> 'required|in:contract,probation,internship,permanent,others,admin',
											'start' 					=> 'required|date_format:"Y-m-d"',
											'end' 						=> 'required_if:status,probation,contract,internship|date_format:"Y-m-d"',
											'reason_end_job' 			=> 'required_with:end',
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
 
        // Career::observe(new CareerObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
