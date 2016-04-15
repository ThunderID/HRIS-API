<?php

namespace App\ThunderID\PersonSystemV1\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use App\Models\Traits\HasSelectAllTrait;
use App\ThunderID\OrganisationManagementV1\Models\Scopes\GlobalScope\ContactScope;

/**
 * Used for Person Models
 * 
 * @author cmooy
 */
class Person extends BaseModel implements AuthenticatableContract, CanResetPasswordContract 
{
    use Authenticatable, CanResetPassword;

	/**
	 * Relationship Traits.
	 *
	 */
	use \App\ThunderID\PersonSystemV1\Models\Traits\HasMany\RelativesTrait;
	use \App\ThunderID\PersonSystemV1\Models\Traits\HasMany\MaritalStatusesTrait;
	use \App\ThunderID\PersonSystemV1\Models\Traits\HasMany\PersonDocumentsTrait;

	use \App\ThunderID\EmploymentSystemV1\Models\Traits\HasMany\WorksTrait;
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
	use \App\ThunderID\PersonSystemV1\Models\Traits\GlobalTrait\HasContactTrait;
	use \App\ThunderID\PersonSystemV1\Models\Traits\GlobalTrait\HasMaritalStatusTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'hrps_persons';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'date_of_birth', 'last_logged_at', 'last_password_updated_at'];

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
	protected $hidden 				= ['password'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'name' 							,
											'place_of_birth' 				,
											'date_of_birth' 				,
											'gender' 						,
											'avatar'						,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'name' 							=> 'required|max:255',
											'place_of_birth' 				=> 'max:255',
											'date_of_birth' 				=> 'date_format:"Y-m-d H:i:s"',
											'gender' 						=> 'in:female,male',
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

        static::addGlobalScope(new ContactScope);
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
