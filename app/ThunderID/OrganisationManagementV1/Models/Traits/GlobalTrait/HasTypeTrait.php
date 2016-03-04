<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait;

/**
 * available function to get Type of records
 *
 * @author cmooy
 */
trait HasTypeTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasTypeTraitConstructor()
	{
		//
	}

	/**
	 * scope to get condition where Type
	 *
	 * @param string or array of products' Type
	 **/
	public function scopeType($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn($query->getModel()->table.'.type', $variable);
		}

		return $query->where($query->getModel()->table.'.type', $variable);
	}
}