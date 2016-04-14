<?php

namespace App\Libraries;

use Illuminate\Support\MessageBag;
use App\ThunderID\EmploymentSystemV1\Models\Employee;

/**
 * Class libraries to validate and parse policy
 *
 * @author cmooy
 */
class UsernameGenerator
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
	public static function generate($code, $id, $name)
	{
		$original		= explode(' ', strtolower($name));
		$modify			= $original[0];
		$countog		= count($original)-1;

		foreach ($original as $keyx => $valuex) 
		{
			if(is_array($valuex) || $valuex!='')
			{
				$countog 				= $keyx;
			}
		}

		$idxuname						= 0;
		
		do
		{
			$uname						= Employee::username($modify.'.'.$code)->first();

			if($uname)
			{
				if(isset($original[$countog]))
				{
					$modify 			= $modify.$original[$countog][$idxuname];
				}
				else
				{
					$modify 			= $modify.$modify;
				}

				$idxuname++;
			}
		}
		while($uname);

		return $modify.'.'.$code;
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
}