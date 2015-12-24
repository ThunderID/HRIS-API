<?php

namespace App\Models;

// use App\Models\Observers\BranchObserver;

class Branch extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table					= 'branches';

	// protected $timestamps			= true;

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at',];

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

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	public static function boot() 
	{
        parent::boot();
 
        // Branch::observe(new BranchObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
