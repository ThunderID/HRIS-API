<?php 

namespace \App\OrganisationManagementV1\Models\Traits\HasMany;

/**
 * Trait for models has many Branches.
 *
 * @author cmooy
 */

trait BranchesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function BranchesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Branches()
	{
		return $this->hasMany('\App\OrganisationManagementV1\Models\Branch');
	}
}