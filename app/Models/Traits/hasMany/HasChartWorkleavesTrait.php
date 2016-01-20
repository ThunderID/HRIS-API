<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many ChartWorkleaves.
 *
 * @author cmooy
 */

trait HasChartWorkleavesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasChartWorkleavesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function ChartWorkleaves()
	{
		return $this->hasMany('App\Models\ChartWorkleave');
	}
}