<?php

namespace App\ThunderID\EmploymentSystemV1\Http\Controllers;

use App\Libraries\JSend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\ThunderID\EmploymentSystemV1\Models\Employee;
use Event;
use App\ThunderID\EmploymentSystemV1\Events\EmployeeCreated;

class EmploymentAccountController extends Controller
{
	/**
	 * get activation link
	 *
	 * 1. get activation link
	 * 2. restrain activation link
	 * @param activation link
	 * @return array of employee
	 */			
	public function getByActivationLink($activation_link) 
	{
		//1. get activation link
		$employee		= Employee::activationlink($activation_link)->first();

		if(!$employee)
		{
			return new JSend('error', Input::all(), ['Link tidak valid']);
		}

		//2. restrain activation link
		$employee->activation_link 	= '';

		if(!$employee->save())
		{
			return new JSend('error', Input::all(), $employee->getError());
		}

		return new JSend('success', ['employee' => $employee->toArray()]);
    }

    /**
	 * resend activation link
	 *
	 * 1. resend activation link
	 * @param employee id
	 * @return array of employee
	 */			
	public function resendActivationLink($id) 
	{
		//1. get activation link
		$employee		= Employee::id($id)->first();

		if(!$employee)
		{
			return new JSend('error', Input::all(), ['Akun tidak valid']);
		}

		$employee 		= $employee->toArray();
		
		Event::fire(new EmployeeCreated($employee));

		return new JSend('success', ['employee' => $employee]);
    }
}
