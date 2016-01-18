<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs To Document.
 *
 * @author cmooy
 */
trait HasDocumentTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasDocumentTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Document()
	{
		return $this->belongsTo('App\Models\Document');
	}
}