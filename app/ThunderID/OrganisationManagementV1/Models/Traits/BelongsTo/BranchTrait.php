<?php 

namespace \App\OrganisationManagementV1\Models\Traits\BelongsTo;

/**
 * Trait for models belongs To Branch.
 *
 * @author cmooy
 */
trait BranchTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function BranchTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Branch()
	{
		return $this->belongsTo('App\BranchManagementV1\Models\Branch');
	}

	/**
	 * check if model has Branch in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeBranchID($query, $variable)
	{
		return $query->where($this->getTable().'.branch_id', $variable);
	}
}