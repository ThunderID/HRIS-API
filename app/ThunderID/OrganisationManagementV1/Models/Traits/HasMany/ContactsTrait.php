<?php 

namespace \App\OrganisationManagementV1\Models\Traits\HasMany;

/**
 * Trait for models has many Contacts.
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
	 * call has many relationship
	 *
	 **/
	public function Contacts()
	{
		return $this->hasMany('\App\OrganisationManagementV1\Models\Contact');
	}
}