<?php 

namespace App\ThunderID\PersonSystemV1\Models\Traits\HasMany;

/**
 * Trait for models has many Person Documents.
 *
 * @author cmooy
 */
trait PersonDocumentsTrait 
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
		return $this->hasMany('App\ThunderID\PersonSystemV1\Models\PersonDocument', 'person_id');
	}

	// /**
	//  * call has many relationship for ancestors
	//  *
	//  **/
	// public function PrivateDocuments()
	// {
	// 	return $this->hasMany('App\ThunderID\PersonSystemV1\Models\PrivateDocument', 'person_id');
	// }
}
