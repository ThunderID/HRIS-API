<?php 

namespace App\Models\Traits;

use App\Models\Scopes\SelectAllScope;

/**
 * Apply scope to get default image
 *
 * @author cmooy
 */
trait HasSelectAllTrait 
{
    /**
     * Boot the Has SelectAll trait for a model has default image.
     *
     * @return void
     */
    public static function bootHasSelectAllTrait()
    {
        static::addGlobalScope(new SelectAllScope);
    }
}