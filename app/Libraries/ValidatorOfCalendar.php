<?php

namespace App\Libraries;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;

/**
 * Class libraries to validate and parse policy
 *
 * @author cmooy
 */
class ValidatorOfCalendar
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
	public function validate($array_of_calendar)
	{
		$rules 					= [
									'day' 		=> 'required|max:255|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday,minggu,senin,selasa,rabu,kamis,jumat,sabtu',
									'break' 	=> 'required|numeric',
								  ];

		foreach ($array_of_calendar['workdays'] as $key => $value) 
		{
			if(isset($array_of_calendar['breaks'][$key]))
			{
				$validator				= Validator::make(['day' => $value, 'break' => $array_of_calendar['breaks'][$key]], $rules);

				if (!$validator->passes())
				{
					$this->errors->add('Code', $validator->errors());
					
					return false;
				}
			}
			else
			{
				$this->errors->add('Code', 'Tidak ada jam istirahat untuk hari '.$value);
					
				return false;
			}
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
}