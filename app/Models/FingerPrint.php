<?php

namespace App\Models;

// use App\Models\Observers\FingerPrintObserver;

/**
 * Used for FingerPrint Models
 * 
 * @author cmooy
 */
class FingerPrint extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasBranchTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table					= 'finger_prints';

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
											'branch_id'						,
											'left_thumb'					,
											'left_index_finger'				,
											'left_middle_finger'			,
											'left_ring_finger'				,
											'left_little_finger'			,
											'right_thumb'					,
											'right_index_finger'			,
											'right_middle_finger'			,
											'right_ring_finger'				,
											'right_little_finger'			,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'branch_id'						=> 'required|exists:branches,id',
											'left_thumb'					=> 'boolean',
											'left_index_finger'				=> 'boolean',
											'left_middle_finger'			=> 'boolean',
											'left_ring_finger'				=> 'boolean',
											'left_little_finger'			=> 'boolean',
											'right_thumb'					=> 'boolean',
											'right_index_finger'			=> 'boolean',
											'right_middle_finger'			=> 'boolean',
											'right_ring_finger'				=> 'boolean',
											'right_little_finger'			=> 'boolean',
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
 
        // FingerPrint::observe(new FingerPrintObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
