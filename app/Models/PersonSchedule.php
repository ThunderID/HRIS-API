<?php

namespace App\Models;

// use App\Models\Observers\PersonScheduleObserver;

/**
 * Used for PersonSchedule Models
 * 
 * @author cmooy
 */
class PersonSchedule extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasPersonTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table					= 'person_schedules';

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
	protected $dates					=	['created_at', 'updated_at', 'deleted_at', 'on'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends					=	[];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden				=	['created_at', 'updated_at', 'deleted_at', 'created_by'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'person_id'						,
											'created_by'					,
											'name'							,
											'status'						,
											'on'							,
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
											'person_id'						=> 'exists:persons,id',
											'created_by'					=> 'exists:persons,id',
											'name'							=> 'max:255',
											'status'						=> 'in:DN,SS,SL,CN,CB,CI,UL,HB,L',
											'on'							=> 'date_format:"Y-m-d H:i:s"',
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


	/**
	 * find range
	 * 
	 * @param array or singular date
	 */	
	public function scopeOnDate($query, $variable)
	{
		if(is_array($variable))
		{
			$started_at 	= date('Y-m-d H:i:s', strtotime($variable[0]));
			$ended_at		= date('Y-m-d H:i:s', strtotime($variable[1]));

			return $query->where('on', '>=', $started_at)
						->where('on', '<=', $ended_at)
						;
		}
		
		$ondate 			= date('Y-m-d', strtotime($variable));

		return $query->where('on', '=', $ondate);
	}
}
