<?php 

namespace App\Models\Traits;

use App\Models\Scopes\TypeScope;

/**
 * Apply scope to get type of inheritance model
 *
 * @author cmooy
 */
trait HasTypeTrait 
{
    /**
     * Boot the Has Type trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasTypeTrait()
    {
        static::addGlobalScope(new TypeScope);
    }
}