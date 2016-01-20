<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs To Chart.
 *
 * @author cmooy
 */
trait HasChartTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasChartTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Chart()
	{
		return $this->belongsTo('App\Models\Chart');
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