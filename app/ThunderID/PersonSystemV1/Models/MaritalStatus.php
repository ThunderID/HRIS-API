<?php

namespace App\ThunderID\PersonSystemV1\Models;

/**
 * Used for MaritalStatus Models
 * 
 * @author cmooy
 */
class MaritalStatus extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */

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
	protected $table					= 'marital_statuses';

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
	protected $dates					=	['created_at', 'updated_at', 'deleted_at', 'ondate'];

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
											'status'						,
											'ondate'						,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'status'						=> 'required|max:255',
											'ondate'						=> 'date_format:"Y-m-d"',
										];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	/**
	 * boot
	 *
	 */	
	public static function boot() 
	{
        parent::boot();
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
