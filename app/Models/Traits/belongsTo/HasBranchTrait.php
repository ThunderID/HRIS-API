<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models has many Branch.
 *
 * @author cmooy
 */
trait HasBranchTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasBranchTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Branch()
	{
		return $this->belongsTo('App\Models\Branch');
	}

	/**
	 * check if model has branch in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeBranchID($query, $variable)
	{
		return $query->where('branch_id', $variable);
	}
}