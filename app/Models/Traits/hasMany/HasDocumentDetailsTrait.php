<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Document Details.
 *
 * @author cmooy
 */
trait HasDocumentDetailsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasDocumentDetailsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function DocumentDetails()
	{
		return $this->hasMany('App\Models\DocumentDetail');
	}
}