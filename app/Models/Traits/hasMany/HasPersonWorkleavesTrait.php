<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many PersonWorkleaves.
 *
 * @author cmooy
 */

trait HasPersonWorkleavesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPersonWorkleavesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function PersonWorkleaves()
	{
		return $this->hasMany('App\Models\PersonWorkleave', 'person_id');
	}
}