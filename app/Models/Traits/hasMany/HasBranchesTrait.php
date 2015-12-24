<?php namespace App\Models\Traits\hasMany;

trait HasBranchesTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasBranchesTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN Branche PACKAGE -------------------------------------------------------------------*/

	public function Branches()
	{
		return $this->hasMany('App\Models\Branch');
	}
}