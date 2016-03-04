<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait;

/**
 * available function to get name of records
 *
 * @author cmooy
 */
trait HasNameTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasNameTraitConstructor()
	{
		//
	}

	/**
	 * scope to get condition where name
	 *
	 * @param string or array of name
	 **/
	public function scopeName($query, $variable)
	{
		if(is_array($variable))
		{
			foreach ($variable as $key => $value) 
			{
				$query = $query->where($query->getModel()->table.'.name', 'like', '%'.$value.'%');
			}

			return $query;
		}
		return 	$query->where($query->getModel()->table.'.name', 'like', '%'.$variable.'%');
	}
}