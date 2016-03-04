<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait;

/**
 * available function to get Default of records
 *
 * @author cmooy
 */
trait HasDefaultTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasDefaultTraitConstructor()
	{
		//
	}

	/**
	 * scope to get condition where Default
	 *
	 * @param string or array of products' Default
	 **/
	public function scopeDefault($query, $variable)
	{
		return 	$query->where($query->getModel()->table.'.is_default', true);
	}
}