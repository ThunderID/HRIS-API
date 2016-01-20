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
		if(in_array(get_class($this), ['App\Models\PersonDocument', 'App\Models\PrivateDocument', 'App\Models\EmploymentDocument']))
		{
			return $this->hasMany('App\Models\DocumentDetail', 'person_document_id');
		}

		return $this->hasMany('App\Models\DocumentDetail');
	}
}