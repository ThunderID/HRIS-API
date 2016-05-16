<?php 

namespace App\ThunderID\WorkforceManagementV1\Models\Traits\HasMany;

/**
 * Trait for models has many Schedules.
 *
 * @author cmooy
 */
trait SchedulesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function SchedulesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Schedules()
	{
		return $this->hasMany('\App\ThunderID\WorkforceManagementV1\Models\Schedule');
	}
}