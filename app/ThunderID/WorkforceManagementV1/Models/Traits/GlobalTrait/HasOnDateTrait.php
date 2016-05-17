<?php 

namespace App\ThunderID\WorkforceManagementV1\Models\Traits\GlobalTrait;

/**
 * available function to get OnDate of records
 *
 * @author cmooy
 */
trait HasOnDateTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasOnDateTraitConstructor()
	{
		//
	}

	/**
	 * scope to get condition where OnDate
	 *
	 * @param string or array of OnDate
	 **/
	public function scopeOnDate($query, $variable)
	{
		if(is_array($variable))
		{
			$started_at 	= Carbon::parse($variable[0])->format('Y-m-d H:i:s');
			$ended_at 		= Carbon::parse($variable[1])->format('Y-m-d H:i:s');

			return $query->where('ondate', '>=', $started_at)
						->where('ondate', '<=', $ended_at)
						;
		}
		
		$ondate 			= Carbon::parse($variable)->format('Y-m-d H:i:s');

		return $query->where('ondate', '=', $ondate);
	}
}