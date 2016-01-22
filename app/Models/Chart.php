<?php

namespace App\Models;

use App\Models\Observers\ChartObserver;

/**
 * Used for Chart Models
 * 
 * @author cmooy
 */
class Chart extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasBranchTrait;
	use \App\Models\Traits\hasMany\HasWorksTrait;
	use \App\Models\Traits\hasMany\HasFollowsTrait;
	use \App\Models\Traits\hasMany\HasChartWorkleavesTrait;

	use \App\Models\Traits\belongsToMany\HasCalendarsTrait;
	use \App\Models\Traits\belongsToMany\HasWorkleavesTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'charts';

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
											'branch_id'						,
											'chart_id'						,
											'name'							,
											'path'							,
											'grade'							,
											'tag'							,
											'min_employee'					,
											'ideal_employee'				,
											'max_employee'					,
											'current_employee'				,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'branch_id'						=> 'exists:branches,id',
											// 'chart_id'						=> 'exists:charts,id',
											'name'							=> 'max:255',
											'path'							=> 'max:255',
											'grade'							=> 'numeric',
											'tag'							=> 'max:255',
											'min_employee'					=> 'numeric',
											'ideal_employee'				=> 'numeric',
											'max_employee'					=> 'numeric',
											'current_employee'				=> 'numeric',
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
 
        Chart::observe(new ChartObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
