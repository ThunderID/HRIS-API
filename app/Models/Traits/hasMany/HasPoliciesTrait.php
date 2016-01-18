<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Policies.
 *
 * @author cmooy
 */
trait HasPoliciesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPoliciesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Policies()
	{
		return $this->hasMany('App\Models\Policy');
	}
}