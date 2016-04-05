<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Traits\HasMany;

/**
 * Trait for models has many Works.
 *
 * @author cmooy
 */
trait WorksTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function WorksTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Works()
	{
		return $this->hasMany('\App\ThunderID\EmploymentSystemV1\Models\Work', 'person_id');
	}
}