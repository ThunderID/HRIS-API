<?php 

namespace App\Models\Traits;

use App\Models\Scopes\EmployeeScope;

trait HasEmployeeTrait 
{
    /**
     * Boot the Has Employee trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasEmployeeTrait()
    {
        static::addGlobalScope(new EmployeeScope);
    }
}