<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\GlobalTrait;

use App\ThunderID\OrganisationManagementV1\Models\Scopes\GlobalScope\ContactScope;

/**
 * Apply scope to get contact of person who work here
 *
 * @author cmooy
 */
trait ContactTrait 
{
    /**
     * Boot the Has Contact trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootContactTrait()
    {
        static::addGlobalScope(new ContactScope);
    }
}