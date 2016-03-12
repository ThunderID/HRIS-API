<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Traits\HasMany;

/**
 * Trait for models has many ContractWorks.
 *
 * @author cmooy
 */
trait ContractWorksTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function ContractWorksTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function ContractWorks()
	{
		return $this->hasMany('\App\ThunderID\EmploymentSystemV1\Models\ContractWork');
	}
}