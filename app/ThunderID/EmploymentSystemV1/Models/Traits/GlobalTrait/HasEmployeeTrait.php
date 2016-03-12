<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Traits\GlobalTrait;

use App\ThunderID\EmploymentSystemV1\Models\Scopes\GlobalScope\EmployeeScope;

/**
 * Apply scope to get person work here
 *
 * @author cmooy
 */
trait HasEmployeeTrait 
{
	/**
	 * Boot the Has Employee trait for a model (used for morph/inherit table).
	 *
	 * @return void
	 */
	public static function bootHasEmployeeTrait()
	{
		static::addGlobalScope(new EmployeeScope);
	}
}