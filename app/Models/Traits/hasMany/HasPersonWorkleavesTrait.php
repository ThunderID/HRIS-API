<?php namespace App\Models\Traits\hasMany;

trait HasPersonWorkleavesTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasPersonWorkleavesTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN PersonWorkleave PACKAGE -------------------------------------------------------------------*/

	public function PersonWorkleaves()
	{
		return $this->hasMany('App\Models\PersonWorkleave');
	}

	public function scopeQuotaWorkleave($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(quota),0) as quota_workleave')
					->join('person_workleaves', function ($join) 
					{
						$join->on ( 'persons.id', '=', 'person_workleaves.person_id' )
						->where('person_workleaves.start', '<=', 'NOW()')
						->where('person_workleaves.end', '<=', 'NOW()')
						->wherenull('person_workleaves.deleted_at')
						;
					})
					;
	}

}