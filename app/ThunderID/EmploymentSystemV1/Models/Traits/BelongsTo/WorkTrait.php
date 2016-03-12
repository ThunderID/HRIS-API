<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Traits\BelongsTo;

/**
 * Trait for models belongs To Work.
 *
 * @author cmooy
 */
trait WorkTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function WorkTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Work()
	{
		return $this->belongsTo('App\ThunderID\EmploymentSystemV1\Models\Work');
	}

	/**
	 * check if model has Work in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeWorkID($query, $variable)
	{
		return $query->where($this->getTable().'.work_id', $variable);
	}
}