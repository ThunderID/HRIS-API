<?php namespace App\Models\Traits\belongsToMany;

/**
 * Trait for models belongs to many Workleaves.
 *
 * @author cmooy
 */
trait HasWorkleavesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasWorkleavesTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto many relationship Workleaves' type
	 *
	 **/
	public function Workleaves()
	{
		return $this->belongsToMany('App\Models\Workleave', 'charts_workleaves', 'chart_id', 'workleave_id');
	}
}