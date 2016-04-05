<?php 

namespace App\ThunderID\PersonSystemV1\Models\Traits\BelongsTo;

/**
 * Trait for models belongs To Relative.
 *
 * @author cmooy
 */
trait RelativeTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function RelativeTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Relative()
	{
		return $this->belongsTo('App\ThunderID\PersonSystemV1\Models\Person', 'relative_id');
	}

	/**
	 * check if model has Relative in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeRelativeID($query, $variable)
	{
		return $query->where($this->getTable().'.relative_id', $variable);
	}
}