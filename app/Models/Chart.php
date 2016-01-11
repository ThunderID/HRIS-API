<?php

namespace App\Models;

// use App\Models\Observers\ChartObserver;

/**
 * Used for Chart Models
 * 
 * @author cmooy
 */
class Chart extends BaseModel
{
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
											'branch_id'						=> 'required|exists:branches,id',
											// 'chart_id'						=> 'required|max:255',
											'name'							=> 'required|max:255',
											'path'							=> 'required|max:255',
											'grade'							=> 'numeric',
											'tag'							=> 'required|max:255',
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
 
        // Chart::observe(new ChartObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
	
	/**
	 * scope to find code of Chart
	 *
	 * @param string of code
	 */
	public function scopeCode($query, $variable)
	{
		return 	$query->where('code', $variable);
	}
}
