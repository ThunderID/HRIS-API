<?php namespace App\Models\Traits\belongsToMany;

/**
 * Trait for models belongs to many Calendars.
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
	 * call belongsto many relationship Calendars' type
	 *
	 **/
	public function Calendars()
	{
		return $this->belongsToMany('App\Models\Calendar', 'follows', 'chart_id', 'calendar_id');
	}
}