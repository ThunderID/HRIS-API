<?php 

namespace App\Models\Traits;

use App\Models\Scopes\TakenWorkleaveScope;

/**
 * Apply scope to get taken workleave here
 *
 * @author cmooy
 */
trait HasTakenWorkleaveTrait 
{
	/**
	 * Boot the Has TakenWorkleave trait for a model (used for morph/inherit table).
	 *
	 * @return void
	 */
	public static function bootHasTakenWorkleaveTrait()
	{
		static::addGlobalScope(new TakenWorkleaveScope);
	}
}