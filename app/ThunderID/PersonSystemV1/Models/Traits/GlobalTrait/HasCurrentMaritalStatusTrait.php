<?php 

namespace App\ThunderID\PersonSystemV1\Models\Traits\GlobalTrait;

use App\ThunderID\PersonSystemV1\Models\Scopes\GlobalScope\CurrentMaritalStatusScope;

/**
 * Apply scope to get current marital status of employee
 *
 * @author cmooy
 */
trait HasCurrentMaritalStatusTrait 
{
	/**
	 * Boot the Has Employee trait for a model (used for morph/inherit table).
	 *
	 * @return void
	 */
	public static function bootHasCurrentMaritalStatusTrait()
	{
		static::addGlobalScope(new CurrentMaritalStatusScope);
	}
}