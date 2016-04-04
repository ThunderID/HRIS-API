<?php namespace App\ThunderID\EmploymentSystemV1\Models\Traits\GlobalTrait;

use Illuminate\Support\Facades\DB;

/**
 * available function to get result of stock
 *
 * @author cmooy
 */
trait HasOrganisationTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasOrganisationTraitConstructor()
	{
		//
	}

	/**
	 * organisation id
	 *
	 **/
	public function scopeOrganisationID($query, $variable)
	{
		return $query
			->where('organisation_id', $variable)
				;
	}

	/**
	 * branch name
	 *
	 **/
	public function scopeBranchName($query, $variable)
	{
		return $query
			->where('hrom_branches.name', $variable)
				;
	}

	/**
	 * chart name
	 *
	 **/
	public function scopeChartName($query, $variable)
	{
		return $query
			->where('hrom_charts.name', $variable)
				;
	}

	/**
	 * department
	 *
	 **/
	public function scopeDepartment($query, $variable)
	{
		return $query
			->where('hrom_charts.department', $variable)
				;
	}
}