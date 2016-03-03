<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\MorphTo;

/**
 * Trait for models morph to image.
 *
 * @author cmooy
 */
trait ContactableTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function ContactableTraitConstructor()
	{
		//
	}

	/**
	 * define morph to as contactable
	 *
	 **/
    public function contactable()
    {
        return $this->morphTo();
    }

	/**
	 * find contactable id
	 *
	 **/
    public function scopeContactableID($query, $variable)
    {
		return $query->where('contactable_id', $variable);
    }

	/**
	 * find contactable type
	 *
	 **/
    public function scopeContactableType($query, $variable)
    {
		return $query->where('contactable_type', $variable);
    }
}