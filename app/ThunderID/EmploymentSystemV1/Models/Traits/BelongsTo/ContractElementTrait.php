<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Traits\BelongsTo;

/**
 * Trait for models belongs To ContractElement.
 *
 * @author cmooy
 */
trait ContractElementTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function ContractElementTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function ContractElement()
	{
		return $this->belongsTo('App\ThunderID\EmploymentSystemV1\Models\ContractElement');
	}

	/**
	 * check if model has ContractElement in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeContractElementID($query, $variable)
	{
		return $query->where($this->getTable().'.contract_element_id', $variable);
	}
}