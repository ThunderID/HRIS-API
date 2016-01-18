<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Documents.
 *
 * @author cmooy
 */
trait HasDocumentsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasDocumentsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Documents()
	{
		return $this->hasMany('App\Models\Document');
	}
}