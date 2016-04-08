<?php

namespace App\ThunderID\EmploymentSystemV1\Http\Controllers;

use App\Libraries\JSend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use \App\ThunderID\EmploymentSystemV1\Models\Employee;

class EmploymentAccountController extends Controller
{
	/**
	 * get activation link
	 *
	 * 1. get join date
	 * 2. get last nik order number
	 * 3. check if user already hath nik
	 * @param code and id
	 * @return $nik
	 */			
	public function getByActivationLink($activation_link) 
	{
		//1. get join date
		$employee		= Employee::activationlink($activation_link)->first();

		if(!$employee)
		{
			return new JSend('error', Input::all(), ['Link tidak valid']);
		}

		$employee->activation_link 	= '';

		if(!$employee->save())
		{
			return new JSend('error', Input::all(), $employee->getError());
		}

		return new JSend('success', ['employee' => $employee->toArray()]);
    }
}
