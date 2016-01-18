<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Person Documents.
 *
 * @author cmooy
 */
trait HasPersonDocumentsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPersonDocumentsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function PersonDocuments()
	{
		return $this->hasMany('App\Models\PersonDocument', 'person_id');
	}

	/**
	 * call has many relationship for ancestors
	 *
	 **/
	public function PrivateDocuments()
	{
		return $this->hasMany('App\Models\PrivateDocument', 'person_id');
	}
}