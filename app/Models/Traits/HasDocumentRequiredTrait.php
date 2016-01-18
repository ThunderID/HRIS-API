<?php 

namespace App\Models\Traits;

use App\Models\Scopes\DocumentRequiredScope;

/**
 * Apply scope to get Required 
 *
 * @author cmooy
 */
trait HasDocumentRequiredTrait 
{
    /**
     * Boot the Has Required trait for a model has Required .
     *
     * @return void
     */
    public static function bootHasDocumentRequiredTrait()
    {
        static::addGlobalScope(new DocumentRequiredScope);
    }
}