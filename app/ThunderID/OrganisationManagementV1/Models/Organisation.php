<?php 

namespace App\ThunderID\OrganisationManagementV1\Models;

use App\ThunderID\OrganisationManagementV1\Models\Observers\OrganisationObserver;

/**
 * Used for Organisation Models
 * 
 * @author cmooy
 */
class Organisation extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\HasMany\BranchesTrait;
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\HasMany\PoliciesTrait;

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
	protected $table				= 'organisations';

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
											'name'							,
											'code'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'name'							=> 'max:255',
											'code'							=> 'max:255',
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
 
        Organisation::observe(new OrganisationObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
