<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Follows.
 *
 * @author cmooy
 */

trait HasFollowsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasFollowsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Follows()
	{
		return $this->hasMany('App\Models\Follow');
	}
}