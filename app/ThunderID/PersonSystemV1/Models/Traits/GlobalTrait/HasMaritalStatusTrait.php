<?php namespace App\ThunderID\PersonSystemV1\Models\Traits\GlobalTrait;

use Illuminate\Support\Facades\DB;

/**
 * available function to get result of stock
 *
 * @author cmooy
 */
trait HasMaritalStatusTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasMaritalStatusTraitConstructor()
	{
		//
	}

	/**
	 * find by marital status
	 *
	 **/
	public function scopeMaritalStatus($query, $variable)
	{
		$prefix_person 				= 'hrps_';

		return $query
			->selectraw($prefix_person.'marital_statuses.status as current_marital_status')
			->where($prefix_person.'marital_statuses.status', $variable)
			->leftjoin(DB::raw($prefix_person.'marital_statuses'), function ($join) use($prefix_person)
			 {
									$join->on ( DB::raw($prefix_person.'persons.id'), '=', DB::raw($prefix_person.'marital_statuses.person_id') )
										->on(DB::raw('('.$prefix_person.'marital_statuses.id = (select id from '.$prefix_person.'marital_statuses as tl2 where tl2.person_id = '.$prefix_person.'marital_statuses.person_id and tl2.deleted_at is null order by tl2.ondate desc limit 1))'), DB::raw(''), DB::raw(''))
										->wherenull( DB::raw($prefix_person.'marital_statuses.deleted_at'))
									;
			})
			;
	}

	/**
	 * get current marital status
	 *
	 **/
	public function scopeCurrentMaritalStatus($query, $variable)
	{
		$prefix_person 				= 'hrps_';

		return $query
			->selectraw($prefix_person.'marital_statuses.status as current_marital_status')
			->leftjoin(DB::raw($prefix_person.'marital_statuses'), function ($join) use($prefix_person)
			 {
									$join->on ( DB::raw($prefix_person.'persons.id'), '=', DB::raw($prefix_person.'marital_statuses.person_id') )
										->on(DB::raw('('.$prefix_person.'marital_statuses.id = (select id from '.$prefix_person.'marital_statuses as tl2 where tl2.person_id = '.$prefix_person.'marital_statuses.person_id and tl2.deleted_at is null order by tl2.updated_at desc limit 1))'), DB::raw(''), DB::raw(''))
										->wherenull( DB::raw($prefix_person.'marital_statuses.deleted_at'))
									;
			})
			;
	}

}