<?php namespace App\Models\Traits\belongsToMany;

/**
 * Trait for models belongs to manu Work.
 *
 * @author cmooy
 */
trait HasFollowedWorksTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasFollowedWorksTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to many relationship
	 *
	 **/
	public function FollowedWorks()
	{
		return $this->belongsToMany('App\Models\Work', 'follow_workleaves', 'workleave_id', 'work_id');
	}
}