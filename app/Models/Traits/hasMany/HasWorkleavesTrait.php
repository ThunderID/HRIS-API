<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Workleaves.
 *
 * @author cmooy
 */

trait HasWorkleavesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasWorkleavesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Workleaves()
	{
		return $this->hasMany('App\Models\Workleave');
	}
}