<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Traits\BelongsTo;

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
		return $this->belongsTo('App\ThunderID\OrganisationManagementV1\Models\Organisation');
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

	/**
	 * check if model has organisation in certain code
	 *
	 * @var array or singular code
	 **/
	public function scopeOrganisationCode($query, $variable)
	{
		return $query->whereHas('organisation', function($q)use($variable){$q->code($variable);});
	}
}