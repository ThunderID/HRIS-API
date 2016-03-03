<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\MorphMany;

trait ContactsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function ContactsTraitConstructor()
	{
		//
	}

	public function Contacts()
	{
		return $this->morphMany('\App\ThunderID\OrganisationManagementV1\Models\Contact', 'contactable')->orderby('is_default','desc');
	}
}
