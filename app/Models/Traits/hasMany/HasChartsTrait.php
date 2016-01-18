<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Charts.
 *
 * @author cmooy
 */
trait HasChartsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasChartsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Charts()
	{
		return $this->hasMany('App\Models\Chart');
	}
}