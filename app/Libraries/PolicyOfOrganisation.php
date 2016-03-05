<?php

namespace App\Libraries;

use Illuminate\Support\MessageBag;
use App\ThunderID\OrganisationManagementV1\Models\Policy;

/**
 * Class libraries to validate and parse policy
 *
 * @author cmooy
 */
class PolicyOfOrganisation
{
	protected $errors;

	public function __construct()
	{
		$this->errors = new MessageBag;
	}

	/**
	 * validate input parameter (need to parse) based on policy code
	 *
	 * @param array of policy (contain code)
	 * @return boolean
	 */
	public function validate($array_of_policy)
	{
		switch ($array_of_policy['code']) 
		{
			default:
				$this->errors->add('Code', 'Peraturan tidak terdaftar.');
				break;
		}

		if($this->errors->count())
		{
			return false;
		}
	
		return true;
	}

	/**
	 * getting protected error
	 *
	 * @return message bag
	 */
	public function getError()
	{
		return $this->errors;
	}

	/**
	 * parse input parameter (validate first) based on policy code
	 *
	 * @param array of policy (contain code)
	 * @return array of policy parsed
	 */
	public function parse($array_of_policy)
	{
		$policy 			= [];
		if($this->validate($array_of_policy))
		{
			switch ($array_of_policy['code']) 
			{
				default:
					break;
			}
		}

		return $policy;
	}

	/**
	 * validate parsed parameter based on policy code on behalf of saving
	 *
	 * @param model of policy
	 * @return boolean
	 */
	public function validatesaving(Policy $policy)
	{
		switch ($policy['code']) 
		{
			default:
				$this->errors->add('Code', 'Peraturan tidak terdaftar.');
				break;
		}

		if($this->errors->count())
		{
			return false;
		}
	
		return true;
	}

	/**
	 * validate parsed parameter based on policy code on behalf of deleting
	 *
	 * @param model of policy
	 * @return boolean
	 */
	public function validatedeleting(Policy $policy)
	{
		switch ($policy['code']) 
		{
			default:
				$this->errors->add('Code', 'Peraturan tidak terdaftar.');
				break;
		}

		if($this->errors->count())
		{
			return false;
		}
	
		return true;
	}
}