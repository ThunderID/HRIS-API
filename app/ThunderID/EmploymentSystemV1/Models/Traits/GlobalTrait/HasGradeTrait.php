<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Traits\GlobalTrait;

use App\ThunderID\EmploymentSystemV1\Models\Scopes\GlobalScope\GradeScope;

/**
 * Apply scope to get person work here
 *
 * @author cmooy
 */
trait HasGradeTrait 
{
	/**
	 * Boot the Has Grade trait for a model (used for morph/inherit table).
	 *
	 * @return void
	 */
	public static function bootHasGradeTrait()
	{
		static::addGlobalScope(new GradeScope);
	}
}