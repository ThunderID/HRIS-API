<?php

namespace App\ThunderID\OrganisationManagementV1\Models;

use App\ThunderID\OrganisationManagementV1\Models\Observers\PolicyObserver;

/**
 * Used for Policy Models
 * 
 * @author cmooy
 */
class Policy extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\BelongsTo\OrganisationTrait;

	/**
	 * Global traits used as scope (plugged scope).
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait\HasNameTrait;
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait\HasCodeTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table					= 'hrom_tmp_policies';

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
	protected $dates					=	['created_at', 'updated_at', 'deleted_at', 'started_at'];

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
											'organisation_id'				,
											'code'							,
											'name'							,
											'parameter'						,
											'action'						,
											'description'					,
											'started_at'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'organisation_id'				=> 'exists:organisations,id',
											'code'							=> 'max:255',
											'name'							=> 'max:255',
											'started_at'					=> 'date_format:"Y-m-d H:i:s"',
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
 
        Policy::observe(new PolicyObserver());
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
			if(!is_null($variable[1]))
			{
				return $query->where('started_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])))
							 ->where('started_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])));
			}
			elseif(!is_null($variable[0]))
			{
				return $query->where('started_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])));
			}
			else
			{
				return $query->where('started_at', '>=', date('Y-m-d H:i:s'));
			}
		}
		return $query->where('started_at', '<=', date('Y-m-d H:i:s', strtotime($variable)));
	}
}
