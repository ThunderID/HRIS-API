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
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasCalendarTrait;
	use \App\Models\Traits\belongsTo\HasChartTrait;

	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */
	public $type_field				=	'status';

	public $type					=	['contract','probation','internship','permanent','others'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'person_id' 				,
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
											'chart_id'					=> 'exists:charts,id',
											'grade' 					=> 'numeric',
											'status' 					=> 'in:contract,probation,internship,permanent,others',
											'start' 					=> 'date_format:"Y-m-d"',
											'end' 						=> 'date_format:"Y-m-d"',
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
