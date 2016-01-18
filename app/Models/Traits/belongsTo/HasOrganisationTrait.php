<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs To Organisation.
 *
 * @author cmooy
 */
trait HasOrganisationTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasOrganisationTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Organisation()
	{
		return $this->belongsTo('App\Models\Organisation');
	}

	/**
	 * check if model has organisation in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeOrganisationID($query, $variable)
	{
		return $query->where('organisation_id', $variable);
	}
}