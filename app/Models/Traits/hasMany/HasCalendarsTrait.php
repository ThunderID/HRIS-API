<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Calendars.
 *
 * @author cmooy
 */

trait HasCalendarsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasCalendarsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Calendars()
	{
		return $this->hasMany('App\Models\Calendar');
	}
}