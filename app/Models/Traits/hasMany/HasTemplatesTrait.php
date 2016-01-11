<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Templates.
 *
 * @author cmooy
 */

trait HasTemplatesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasTemplatesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Templates()
	{
		return $this->hasMany('App\Models\Template');
	}
}