<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Branch Contacts.
 *
 * @author cmooy
 */
trait HasBranchContactsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasBranchContactsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Contacts()
	{
		return $this->hasMany('App\Models\BranchContact', 'branch_id');
	}
}