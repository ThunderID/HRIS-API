<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Person Contacts.
 *
 * @author cmooy
 */
trait HasPersonContactsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPersonContactsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Contacts()
	{
		return $this->hasMany('App\Models\PersonContact', 'person_id');
	}
}