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

	/**
	 * find by grade
	 *
	 **/
	public function scopeGrade($query, $variable)
	{
		$prefix_empl 				= 'hres_';

		return $query
			->selectraw($prefix_empl.'grade_logs.grade as current_work_grade')
			->where($prefix_empl.'grade_logs.grade', $variable)
			->leftjoin(DB::raw($prefix_empl.'grade_logs'), function ($join) use($prefix_empl)
			 {
									$join->on ( DB::raw($prefix_empl.'works.id'), '=', DB::raw($prefix_empl.'grade_logs.work_id') )
										->on(DB::raw('('.$prefix_empl.'grade_logs.id = (select id from '.$prefix_empl.'grade_logs as tl2 where tl2.work_id = '.$prefix_empl.'grade_logs.work_id and tl2.deleted_at is null order by tl2.updated_at desc limit 1))'), DB::raw(''), DB::raw(''))
										->wherenull( DB::raw($prefix_empl.'grade_logs.deleted_at'))
									;
			})
			;
	}

	/**
	 * get current grade
	 *
	 **/
	public function scopeCurrentGrade($query, $variable)
	{
		$prefix_empl 				= 'hres_';
		
		return $query
			->selectraw($prefix_empl.'grade_logs.grade as current_work_grade')
			->leftjoin(DB::raw($prefix_empl.'grade_logs'), function ($join) use($prefix_empl)
			 {
									$join->on ( DB::raw($prefix_empl.'works.id'), '=', DB::raw($prefix_empl.'grade_logs.work_id') )
										->on(DB::raw('('.$prefix_empl.'grade_logs.id = (select id from '.$prefix_empl.'grade_logs as tl2 where tl2.work_id = '.$prefix_empl.'grade_logs.work_id and tl2.deleted_at is null order by tl2.updated_at desc limit 1))'), DB::raw(''), DB::raw(''))
										->wherenull( DB::raw($prefix_empl.'grade_logs.deleted_at'))
									;
			})
			;
	}

}