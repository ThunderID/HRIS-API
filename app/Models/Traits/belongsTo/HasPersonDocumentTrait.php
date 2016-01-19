<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs To PersonDocument.
 *
 * @author cmooy
 */
trait HasPersonDocumentTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPersonDocumentTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function PersonDocument()
	{
		return $this->belongsTo('App\Models\PersonDocument');
	}

	/**
	 * check if model has PersonDocument in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopePersonDocumentID($query, $variable)
	{
		return $query->where($this->getTable().'.person_document_id', $variable);
	}
}