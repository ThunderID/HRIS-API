<?php namespace App\Models\Traits\morphMany;

/**
 * Trait for models has many Contacts.
 *
 * @author cmooy
 */
trait HasOfficeContactsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/

	function HasOfficeContactsTraitConstructor()
	{
		//
	}

	/**
	 * call morph many relationship
	 *
	 **/
	public function Contacts()
	{
		return $this->morphMany('App\Models\Contact', 'branch');
	}
}