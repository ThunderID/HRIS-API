<?php

namespace App\Models;

// use App\Models\Observers\LogObserver;
use App\Models\Traits\HasSelectAllTrait;
use App\Models\Traits\HasEmployeeScheduleTrait;

/**
 * Used for Log Models
 * 
 * @author cmooy
 */
class Log extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasPersonTrait;

	/**
	 * Global traits used as query builder (plugged scope).
	 *
	 */
	use HasSelectAllTrait;
	use HasEmployeeScheduleTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table					= 'logs';

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
	protected $dates					=	['created_at', 'updated_at', 'deleted_at', 'on', 'last_input_time'];

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
	protected $hidden 					= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'person_id'						,
											'created_by'					,
											'name'							,
											'on'							,
											'last_input_time'				,
											'pc'							,
											'app_version'					,
											'ip'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'person_id'						=> 'required|exists:persons,id',
											'created_by'					=> 'exists:persons,id',
											'name'							=> 'required|max:255',
											'on'							=> 'required|date_format:"Y-m-d H:i:s"',
											'last_input_time'				=> 'date_format:"Y-m-d H:i:s"',
											'pc'							=> 'max:255',
											'app_version'					=> 'max:255',
											'ip'							=> 'max:255',
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
 
        // Log::observe(new LogObserver());
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
		}
		else
		{
			$started_at		= date('Y-m-d H:i:s', strtotime($variable));
			$ended_at		= date('Y-m-d H:i:s', strtotime($variable.' + 1 day'));
		}
		
		return $query->where($this->getTable().'.on', '>=', $started_at)
						->where($this->getTable().'.on', '<=', $ended_at)
						;
	}
}
