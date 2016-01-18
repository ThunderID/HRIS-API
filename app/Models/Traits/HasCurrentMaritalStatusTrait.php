<?php 

namespace App\Models\Traits;

use App\Models\Scopes\CurrentMaritalStatusScope;

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