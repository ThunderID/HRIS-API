<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Person Contacts.
 *
 * @author cmooy
 */
trait HasMaritalStatusesTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasMaritalStatusesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function MaritalStatuses()
	{
		return $this->hasMany('App\Models\MaritalStatus', 'person_id');
	}
}