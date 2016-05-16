<?php

namespace App\ThunderID\WorkforceManagementV1\Models;

// use App\Models\Observers\CalendarObserver;

/**
 * Used for Calendar Models
 * 
 * @author cmooy
 */
class Calendar extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\BelongsTo\OrganisationTrait;
	use \App\ThunderID\WorkforceManagementV1\Models\Traits\HasMany\SchedulesTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */

	/**
	 * Global traits used as scope (plugged scope).
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait\HasNameTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'hrwm_calendars';

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
	protected $hidden 				= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'organisation_id'				,
											'name'							,
											'workdays'						,
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
											'organisation_id'				=> 'exists:hrom_organisations,id',
											'name'							=> 'max:255',
											'ondate'						=> 'date_format:"Y-m-d"',
											'start'							=> 'date_format:"H:i:s"',
											'end'							=> 'date_format:"H:i:s"',
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
 
        // Calendar::observe(new CalendarObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
