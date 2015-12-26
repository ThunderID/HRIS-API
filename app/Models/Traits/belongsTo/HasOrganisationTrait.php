<?php namespace App\Models\Traits\belongsTo;

trait HasOrganisationTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasOrganisationTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN Organisation PACKAGE -------------------------------------------------------------------*/

	public function Organisation()
	{
		return $this->belongsTo('App\Models\Organisation');
	}

	public function scopeOrganisationCode($query, $variable)
	{
		return $query->join('organisations', function ($join) use($variable)
				 {
	                                    $join->on ( 'branches.organisation_id', '=', 'organisations.id' )
	                                    ->where('organisations.code', '=', $variable)
	                                    ->wherenull('organisations.deleted_at')
	                                    ;
				});
	}
}