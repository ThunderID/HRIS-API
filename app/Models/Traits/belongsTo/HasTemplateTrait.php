<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models has many Template.
 *
 * @author cmooy
 */
trait HasTemplateTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasTemplateTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Template()
	{
		return $this->belongsTo('App\Models\Template');
	}
}