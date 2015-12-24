<?php 

namespace App\Models\Traits;

use App\Models\Scopes\AuthorizedEmployeeScope;

trait HasAuthorizedEmployeeTrait 
{
    /**
     * Boot the Has AuthorizedEmployee trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasAuthorizedEmployeeTrait()
    {
        static::addGlobalScope(new AuthorizedEmployeeScope);
    }
}