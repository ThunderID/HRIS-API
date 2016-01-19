<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Logs.
 *
 * @author cmooy
 */

trait HasLogsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasLogsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Logs()
	{
		return $this->hasMany('App\Models\Log', 'person_id');
	}
}