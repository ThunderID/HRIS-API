<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait;

/**
 * available function to get code of records
 *
 * @author cmooy
 */
trait HasCodeTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasCodeTraitConstructor()
	{
		//
	}
	
	/**
	 * scope to find code 
	 *
	 * @param string of code
	 */
	public function scopeCode($query, $variable)
	{
		return 	$query->where('code', $variable);
	}
}