<?php 

namespace App\Models\Traits;

use App\Models\Scopes\QuotaWorkleaveScope;

/**
 * Apply scope to get quota of workleave
 *
 * @author cmooy
 */
trait HasQuotaWorkleaveTrait 
{
    /**
     * Boot the Has Employee trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasQuotaWorkleaveTrait()
    {
        static::addGlobalScope(new QuotaWorkleaveScope);
    }
}