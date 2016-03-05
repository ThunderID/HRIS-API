<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\MorphMany;

/**
 * Trait for models morph many contact.
 *
 * @author cmooy
 */
trait ContactsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function ContactsTraitConstructor()
	{
		//
	}

	/**
	 * call morph many relationship
	 *
	 **/
	public function Contacts()
	{
		return $this->morphMany('\App\ThunderID\OrganisationManagementV1\Models\Contact', 'contactable')->orderby('is_default','desc');
	}
}
