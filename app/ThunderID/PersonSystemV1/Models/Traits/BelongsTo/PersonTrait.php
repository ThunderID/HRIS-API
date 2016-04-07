<?php 

namespace App\ThunderID\PersonSystemV1\Models\Traits\BelongsTo;

/**
 * Trait for models belongs To Person.
 *
 * @author cmooy
 */
trait PersonTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function PersonTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Person()
	{
		return $this->belongsTo('App\ThunderID\PersonSystemV1\Models\Person');
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

	/**
	 * check if model has Person in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeNotPersonID($query, $variable)
	{
		return $query->where($this->getTable().'.person_id', '<>', $variable);
	}
}