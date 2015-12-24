<?php namespace App\Models\Traits\hasMany;

trait HasProcessLogsTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasProcessLogsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN ProcessLog PACKAGE -------------------------------------------------------------------*/

	public function ProcessLogs()
	{
		return $this->hasMany('App\Models\ProcessLog');
	}

	public function ProcessLogToday()
	{
		return $this->hasOne('App\Models\ProcessLog', 'person_id')->where('on', '=', date('Y-m-d'));
	}

}