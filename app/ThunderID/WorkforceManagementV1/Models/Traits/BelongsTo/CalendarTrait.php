<?php 

namespace App\ThunderID\WorkforceManagementV1\Models\Traits\BelongsTo;

/**
 * Trait for models belongs To Calendar.
 *
 * @author cmooy
 */
trait CalendarTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function CalendarTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Calendar()
	{
		return $this->belongsTo('App\ThunderID\WorkforceManagementV1\Models\Calendar');
	}

	/**
	 * check if model has Calendar in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeCalendarID($query, $variable)
	{
		return $query->where($this->getTable().'.calendar_id', $variable);
	}

	/**
	 * check if model has person in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeCalendarPersonID($query, $variable)
	{
		return $query->wherehas('Calendar', function($q) use($variable){$q->personid($variable);});
	}
}