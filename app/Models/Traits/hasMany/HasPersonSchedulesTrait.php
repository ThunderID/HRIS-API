<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many PersonSchedules.
 *
 * @author cmooy
 */

trait HasPersonSchedulesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPersonSchedulesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function PersonSchedules()
	{
		return $this->hasMany('App\Models\PersonSchedule', 'person_id');
	}
}