<?php

namespace App\Models;

// use App\Models\Observers\WorkObserver;

/**
 * Used for Work Models
 * 
 * @author cmooy
 */
class Work extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\hasMany\HasWorksTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'works';

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	// protected $timestamps			= true;
	
	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'start', 'end'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends				=	[];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= [];

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
											'position' 					,
											'organisation' 				,
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
											'chart_id'					=> 'required_without:position',
											'grade' 					=> 'max:255',
											'status' 					=> 'required|in:contract,probation,internship,permanent,others,admin,previous',
											'start' 					=> 'required|date_format:"Y-m-d"',
											'end' 						=> 'required_if:status,probation,contract,internship,previous|date_format:"Y-m-d"',
											'position' 					=> 'required_without:chart_id',
											'organisation' 				=> 'required_without:chart_id',
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
 
        // Work::observe(new WorkObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
