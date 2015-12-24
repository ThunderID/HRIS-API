<?php namespace App\Models\Traits\hasMany;

trait HasEmployeesTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasEmployeesTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN Employee PACKAGE -------------------------------------------------------------------*/

	public function Employees()
	{
		return $this->hasMany('App\Models\Employee');
	}

	public function AuthorizedEmployee()
	{
		return $this->hasOne('App\Models\AuthorizedEmployee');
	}

	public function AbsenceToday()
	{
		return $this->hasMany('App\Models\Employee')->wheredoesnthave('processlogtoday', function($q){$q;});
	}

}