<?php 

namespace App\Models\Traits;

use App\Models\Scopes\ContactScope;

/**
 * Apply scope to get person work here
 *
 * @author cmooy
 */
trait HasContactTrait 
{
    /**
     * Boot the Has Contact trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasContactTrait()
    {
        static::addGlobalScope(new ContactScope);
    }
}