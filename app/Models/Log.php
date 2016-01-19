<?php

namespace App\Models;

// use App\Models\Observers\LogObserver;

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
}
