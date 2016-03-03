<?php 

namespace \App\OrganisationManagementV1\Models\Traits\HasMany;

/**
 * Trait for models has many Charts.
 *
 * @author cmooy
 */

trait ChartsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function ChartsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Charts()
	{
		return $this->hasMany('\App\OrganisationManagementV1\Models\Chart');
	}
}