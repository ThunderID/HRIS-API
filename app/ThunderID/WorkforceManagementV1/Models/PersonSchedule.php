<?php

namespace App\ThunderID\WorkforceManagementV1\Models;

// use App\Models\Observers\PersonScheduleObserver;

/**
 * Used for Schedule Models
 * 
 * @author cmooy
 */
class PersonSchedule extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\ThunderID\PersonSystemV1\Models\Traits\BelongsTo\PersonTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */

	/**
	 * Global traits used as scope (plugged scope).
	 *
	 */
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'hrwm_person_schedules';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at'];

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
	protected $hidden				=	['created_at', 'updated_at', 'deleted_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'person_id'						,
											'name'							,
											'status'						,
											'ondate'						,
											'start'							,
											'end'							,
											'break_idle'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'person_id'						=> 'exists:hrps_persons,id',
											'name'							=> 'max:255',
											'status'						=> 'in:DN,CB,UL,HB,L',
											'ondate'						=> 'date_format:"Y-m-d"',
											'start'							=> 'date_format:"H:i:s"',
											'end'							=> 'date_format:"H:i:s"',
											'break_idle'					=> 'numeric',
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
 
        // PersonSchedule::observe(new PersonScheduleObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
