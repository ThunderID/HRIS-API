<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Employees.
 *
 * @author cmooy
 */
trait HasEmployeesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasEmployeesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
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