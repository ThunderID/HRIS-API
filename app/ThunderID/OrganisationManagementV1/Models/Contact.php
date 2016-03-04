<?php

namespace App\ThunderID\OrganisationManagementV1\Models;

use App\ThunderID\OrganisationManagementV1\Models\Observers\ContactObserver;

/**
 * Used for personcontact and branchcontact
 * 
 * @author cmooy
 */
class Contact extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\MorphTo\ContactableTrait;

	/**
	 * Global traits used as scope (plugged scope).
	 *
	 */
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait\HasDefaultTrait;
	use \App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait\HasTypeTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'contacts';

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
											'contactable_id'				,
											'contactable_type'				,
											'type'							,
											'value'							,
											'is_default'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'contactable_id'				=> 'exists:branches,id',
											'contactable_type'				=> 'max:255|in:App\ThunderID\OrganisationManagementV1\Models\Branch',
											'type'							=> 'max:255|in:email,phone,address,whatsapp,line,facebook,twitter,linkedin',
											'is_default'					=> 'boolean',
										];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	public static function boot() 
	{
        parent::boot();
 
        Contact::observe(new ContactObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
