<?php 

namespace App\Models\Traits;

use App\Models\Scopes\GivenWorkleaveScope;

/**
 * Apply scope to get given workleave here
 *
 * @author cmooy
 */
trait HasGivenWorkleaveTrait 
{
	/**
	 * Boot the Has GivenWorkleave trait for a model (used for morph/inherit table).
	 *
	 * @return void
	 */
	public static function bootHasGivenWorkleaveTrait()
	{
		static::addGlobalScope(new GivenWorkleaveScope);
	}
}