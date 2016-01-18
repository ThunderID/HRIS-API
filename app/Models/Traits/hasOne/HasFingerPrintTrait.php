<?php namespace App\Models\Traits\hasOne;

/**
 * Trait for models has one FingerPrint.
 *
 * @author cmooy
 */
trait HasFingerPrintTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasFingerPrintTraitConstructor()
	{
		//
	}

	/**
	 * call has one relationship
	 *
	 **/
	public function FingerPrint()
	{
		return $this->hasOne('App\Models\FingerPrint', 'branch_id');
	}
}