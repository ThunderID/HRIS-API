<?php

namespace App\Libraries;

use Illuminate\Support\MessageBag;
use App\ThunderID\OrganisationManagementV1\Models\Policy;

class PolicyOfOrganisation
{
	protected $errors;

	public function __construct()
	{
		$this->errors = new MessageBag;
	}

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

	public function getError()
	{
		return $this->errors;
	}

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