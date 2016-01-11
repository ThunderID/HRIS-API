<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Schedules.
 *
 * @author cmooy
 */

trait HasSchedulesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasSchedulesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Schedules()
	{
		return $this->hasMany('App\Models\Schedule');
	}
}