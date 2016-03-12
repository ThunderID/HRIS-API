<?php 

namespace App\ThunderID\PersonSystemV1\Models\Traits\HasMany;

/**
 * Trait for models has many Relatives.
 *
 * @author cmooy
 */
trait RelativesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function RelativesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Relatives()
	{
		return $this->hasMany('\App\ThunderID\PersonSystemV1\Models\Relative');
	}
}