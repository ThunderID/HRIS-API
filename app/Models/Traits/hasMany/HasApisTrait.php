<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Apis.
 *
 * @author cmooy
 */
trait HasApisTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasApisTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Apis()
	{
		return $this->hasMany('App\Models\Api');
	}
}