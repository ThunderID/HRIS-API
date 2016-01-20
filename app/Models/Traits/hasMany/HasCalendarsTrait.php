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
		if(get_class($this)=='App\Models\Calendars')
		{
			return $this->hasMany('App\Models\Calendar', 'import_from_id');
		}

		return $this->hasMany('App\Models\Calendar');
	}
}