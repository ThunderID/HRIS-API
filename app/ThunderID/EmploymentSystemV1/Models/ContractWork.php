<?php

namespace App\ThunderID\EmploymentSystemV1\Models;

/**
 * Used for ContractWork Models
 * 
 * @author cmooy
 */
class ContractWork extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\ThunderID\EmploymentSystemV1\Models\Traits\BelongsTo\ContractElementTrait;
	use \App\ThunderID\EmploymentSystemV1\Models\Traits\BelongsTo\WorkTrait;

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
	protected $table					= 'hres_contracts_works';

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
											'contract_element_id'			,
											'work_id'						,
											'value'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'value'							=> 'required|max:255',
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
