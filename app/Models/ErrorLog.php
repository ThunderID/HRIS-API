<?php

namespace App\Models;

// use App\Models\Observers\ErrorLogObserver;

/**
 * Used for ErrorLog Models
 * 
 * @author cmooy
 */
class ErrorLog extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasOrganisationTrait;

	/**
	 * Global traits used as query builder (plugged scope).
	 *
	 */

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table					= 'error_logs';

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
	protected $hidden 					= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'organisation_id'		,
											'email'					,
											'name'					,
											'pc'					,
											'on'					,
											'message'				,
											'ip'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'organisation_id'				=> 'exists:organisations,id',
											'email'							=> 'max:255',
											'name'							=> 'max:255',
											'pc'							=> 'max:255',
											'on'							=> 'date_format:"Y-m-d H:i:s"',
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
 
        // ErrorLog::observe(new ErrorLogObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
