<?php

namespace App\ThunderID\EmploymentSystemV1\Models;

/**
 * Used for Work Models
 * 
 * @author cmooy
 */
class Work extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\BelongsTo\ChartTrait;
	use \App\ThunderID\PersonSystemV1\Models\Traits\BelongsTo\PersonTrait;
	use \App\ThunderID\EmploymentSystemV1\Models\Traits\HasMany\ContractWorksTrait;

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
	protected $table					= 'hres_works';

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
	protected $dates					=	['created_at', 'updated_at', 'deleted_at', 'start', 'end'];

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
											'chart_id'						,
											'person_id'						,
											'nik'							,
											'status'						,
											'start'							,
											'end'							,
											'reason_end_job'				,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'nik'							=> 'required|max:255',
											'status'						=> 'required|max:255',
											'start'							=> 'required|date_format:"Y-m-d H:i:s"',
											'end'							=> 'date_format:"Y-m-d H:i:s"',
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
