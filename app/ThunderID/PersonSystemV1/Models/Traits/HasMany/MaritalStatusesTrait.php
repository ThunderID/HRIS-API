<?php 

namespace App\ThunderID\PersonSystemV1\Models\Traits\HasMany;

/**
 * Trait for models has many Relatives.
 *
 * @author cmooy
 */
trait MaritalStatusesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function MaritalStatusesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function MaritalStatuses()
	{
		return $this->hasMany('\App\ThunderID\PersonSystemV1\Models\MaritalStatus', 'person_id');
	}
}