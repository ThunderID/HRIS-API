<?php

namespace App\Models;

// use App\Models\Observers\ScheduleObserver;

/**
 * Used for Schedule Models
 * 
 * @author cmooy
 */
class Schedule extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasCalendarTrait;
	use \App\Models\Traits\hasMany\HasSchedulesTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'tmp_schedules';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'on'];

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
											'name'							,
											'status'						,
											'on'							,
											'start'							,
											'end'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'name'							=> 'required|max:255',
											'status'						=> 'required|in:DN,CB,UL,HB,L',
											'on'							=> 'required|date_format:"Y-m-d H:i:s"',
											'start'							=> 'required',
											'end'							=> 'required',
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
 
        // Schedule::observe(new ScheduleObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
