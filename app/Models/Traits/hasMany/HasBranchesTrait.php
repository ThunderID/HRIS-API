<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Branches.
 *
 * @author cmooy
 */

trait HasBranchesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasBranchesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Branches()
	{
		return $this->hasMany('App\Models\Branch');
	}
}