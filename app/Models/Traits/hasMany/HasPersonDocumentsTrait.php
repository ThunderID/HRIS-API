<?php namespace App\Models\Traits\hasMany;

trait HasPersonDocumentsTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasPersonDocumentsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN PersonDocument PACKAGE -------------------------------------------------------------------*/

	public function PersonDocuments()
	{
		return $this->hasMany('App\Models\PersonDocument');
	}

	public function scopeQuotaDocument($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(quota),0) as quota_Document')
					->join('person_Documents', function ($join) 
					{
						$join->on ( 'persons.id', '=', 'person_Documents.person_id' )
						->where('person_Documents.start', '<=', 'NOW()')
						->where('person_Documents.end', '<=', 'NOW()')
						->wherenull('person_Documents.deleted_at')
						;
					})
					;
	}

}