<?php

namespace App\ThunderID\OrganisationManagementV1\Models;

use App\ThunderID\OrganisationManagementV1\Models\Observers\ChartObserver;

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
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\BelongsTo\ChartTrait;
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\BelongsTo\BranchTrait;
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\HasMany\ChartsTrait;

	/**
	 * Global traits used as scope (plugged scope).
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait\HasNameTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'hrom_charts';

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
											'department'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'branch_id'						=> 'exists:hrom_branches,id',
											// 'chart_id'						=> 'exists:charts,id',
											'name'							=> 'max:255',
											'path'							=> 'max:255',
											'department'					=> 'max:255',
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

	/**
	 * scope to get condition where department
	 *
	 * @param string or array of products' department
	 **/
	public function scopeDepartment($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn($query->getModel()->table.'.department', $variable);
		}
		
		return 	$query->where($query->getModel()->table.'.department', $variable);
	}
}
