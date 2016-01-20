<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs To Workleave.
 *
 * @author cmooy
 */
trait HasWorkleaveTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasWorkleaveTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Workleave()
	{
		return $this->belongsTo('App\Models\Workleave');
	}

	/**
	 * check if model has Workleave in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeWorkleaveID($query, $variable)
	{
		return $query->where($this->getTable().'.workleave_id', $variable);
	}
}