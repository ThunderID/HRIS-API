<?php namespace App\ThunderID\EmploymentSystemV1\Models\Traits\GlobalTrait;

use Illuminate\Support\Facades\DB;

/**
 * available function to get result of stock
 *
 * @author cmooy
 */
trait HasWorkTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasWorkTraitConstructor()
	{
		//
	}

	/**
	 * nik
	 *
	 **/
	public function scopeNIK($query, $variable)
	{
		return $query
			->where('nik', $variable)
				;
	}

	/**
	 * work status
	 *
	 **/
	public function scopeWorkStatus($query, $variable)
	{
		return $query
			->where('hres_works.status', $variable)
				;
	}

	/**
	 * work start
	 *
	 **/
	public function scopeWorkStart($query, $variable)
	{
		if(!is_array($variable))
		{
			\App::abort(404);
		}

		return $query
			->where('hres_works.start', '>=', $variable[0])
			->where('hres_works.start', '<=', $variable[1])
				;
	}

	/**
	 * work end
	 *
	 **/
	public function scopeWorkEnd($query, $variable)
	{
		if(!is_array($variable))
		{
			\App::abort(404);
		}

		return $query
			->where('hres_works.end', '>=', $variable[0])
			->where('hres_works.end', '<=', $variable[1])
				;
	}
}