<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs To Person.
 *
 * @author cmooy
 */
trait HasPersonTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPersonTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Person()
	{
		return $this->belongsTo('App\Models\Person');
	}

	/**
	 * check if model has Person in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopePersonID($query, $variable)
	{
		return $query->where($this->getTable().'.person_id', $variable);
	}
}