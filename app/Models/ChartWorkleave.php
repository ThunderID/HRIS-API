<?php

namespace App\Models;

// use App\Models\Observers\ChartWorkleaveObserver;

/**
 * Used for Private and employment document
 * 
 * @author cmooy
 */
class ChartWorkleave extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasWorkleaveTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'charts_workleaves';

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
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'workleave_id'					,
											'chart_id'						,
										];
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'workleave_id'					=> 'exists:tmp_workleaves,id',
											'chart_id'						=> 'exists:charts,id',
										];
	
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
 
        // ChartWorkleave::observe(new ChartWorkleaveObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
