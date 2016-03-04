<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\BelongsTo;

/**
 * Trait for models belongs To Chart.
 *
 * @author cmooy
 */
trait ChartTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function ChartTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Chart()
	{
		return $this->belongsTo('App\ThunderID\OrganisationManagementV1\Models\Chart');
	}

	/**
	 * check if model has Chart in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeChartID($query, $variable)
	{
		return $query->where($this->getTable().'.chart_id', $variable);
	}
}