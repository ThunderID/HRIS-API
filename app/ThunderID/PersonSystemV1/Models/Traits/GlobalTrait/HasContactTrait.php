<?php namespace App\ThunderID\PersonSystemV1\Models\Traits\GlobalTrait;

use Illuminate\Support\Facades\DB;

/**
 * available function to get result of stock
 *
 * @author cmooy
 */
trait HasContactTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasContactTraitConstructor()
	{
		//
	}

	/**
	 * Contact id
	 *
	 **/
	public function scopeEmail($query, $variable)
	{
		return $query
			// ->where('type', 'email')
			// ->where('value', $variable)
				;
	}
}