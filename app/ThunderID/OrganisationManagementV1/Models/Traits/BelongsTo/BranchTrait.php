<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\BelongsTo;

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
		return $this->belongsTo('App\ThunderID\OrganisationManagementV1\Models\Branch');
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

	/**
	 * check if model has organisation id of branch
	 *
	 * @var array or singular id
	 **/
	public function scopeBranchOrganisationID($query, $variable)
	{
		return $query->whereHas('branch', function($q)use($variable){$q->organisationid($variable);});
	}

	/**
	 * check if model has organisation code of branch
	 *
	 * @var array or singular code
	 **/
	public function scopeBranchOrganisationCode($query, $variable)
	{
		return $query->whereHas('branch', function($q)use($variable){$q->organisationcode($variable);});
	}
}