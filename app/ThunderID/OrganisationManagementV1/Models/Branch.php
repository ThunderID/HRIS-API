<?php

namespace App\ThunderID\OrganisationManagementV1\Models;

use App\Models\Traits\HasSelectAllTrait;
use App\ThunderID\OrganisationManagementV1\Models\Scopes\GlobalScope\ContactScope;

use App\ThunderID\OrganisationManagementV1\Models\Observers\BranchObserver;

/**
 * Used for Branch Models
 * 
 * @author cmooy
 */
class Branch extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\BelongsTo\OrganisationTrait;
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\HasMany\ChartsTrait;
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\MorphMany\ContactsTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasSelectAllTrait;

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
	protected $table					= 'hrom_branches';

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
	protected $dates					=	['created_at', 'updated_at', 'deleted_at'];

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
											'name'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'organisation_id'				=> 'exists:organisations,id',
											'name'							=> 'max:255',
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
 
        static::addGlobalScope(new ContactScope);

        Branch::observe(new BranchObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
