<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Works.
 *
 * @author cmooy
 */

trait HasWorksTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasWorksTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Works()
	{
		return $this->hasMany('App\Models\Work');
	}
}