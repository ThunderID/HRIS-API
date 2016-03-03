<?php 

namespace \App\OrganisationManagementV1\Models\Traits\HasMany;

/**
 * Trait for models has many Policies.
 *
 * @author cmooy
 */

trait PoliciesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function PoliciesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Policies()
	{
		return $this->hasMany('\App\OrganisationManagementV1\Models\Policy');
	}
}