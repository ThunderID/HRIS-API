<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs To Calendar.
 *
 * @author cmooy
 */
trait HasCalendarTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasCalendarTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Calendar()
	{
		return $this->belongsTo('App\Models\Calendar');
	}

	/**
	 * check if model has Calendar in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeCalendarID($query, $variable)
	{
		if(get_class($this)=='App\Models\Calendar')
		{
			return $query->where($this->getTable().'.import_from_id', $variable);
		}

		return $query->where($this->getTable().'.calendar_id', $variable);
	}
}