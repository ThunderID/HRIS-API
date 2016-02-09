<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs To ProcessLog.
 *
 * @author cmooy
 */
trait HasProcessLogTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasProcessLogTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function ProcessLog()
	{
		return $this->belongsTo('App\Models\ProcessLog');
	}

	/**
	 * check if model has ProcessLog in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeProcessLogID($query, $variable)
	{
		return $query->where($this->getTable().'.process_log_id', $variable);
	}
}