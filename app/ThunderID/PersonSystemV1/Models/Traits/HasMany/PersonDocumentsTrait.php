<?php 

namespace App\ThunderID\PersonSystemV1\Models\Traits\HasMany;

/**
 * Trait for models has many Relatives.
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
	function PersonDocumentsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function PersonDocuments()
	{
		return $this->hasMany('\App\ThunderID\PersonSystemV1\Models\PersonDocument');
	}
}