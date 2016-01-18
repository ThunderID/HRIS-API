<?php namespace App\Models\Traits\belongsToMany;

/**
 * Trait for models belongs to many Persons.
 *
 * @author cmooy
 */
trait HasPersonsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPersonsTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto many relationship Persons
	 *
	 **/
	public function Persons()
	{
		return $this->belongsToMany('App\Models\Person', 'persons_documents');
	}
}