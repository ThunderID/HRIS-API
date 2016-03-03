<?php 

namespace \App\OrganisationManagementV1\Models\Traits\BelongsTo;

/**
 * Trait for models belongs To Organisation.
 *
 * @author cmooy
 */
trait OrganisationTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function OrganisationTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Organisation()
	{
		return $this->belongsTo('App\OrganisationManagementV1\Models\Organisation');
	}

	/**
	 * check if model has organisation in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeOrganisationID($query, $variable)
	{
		return $query->where($this->getTable().'.organisation_id', $variable);
	}
}