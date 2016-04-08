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
		// $employee->activation_link 	= '';

		// if(!$employee->save())
		// {
		// 	return new JSend('error', Input::all(), $employee->getError());
		// }

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

	/**
	 * set pasword
	 *
	 * 1. get activation link
	 * 2. validate activation
	 * @param activation link
	 * @return array of employee
	 */			
	public function setPassword($activation_link) 
	{
		if(!Input::has('activation'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data activation.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate activation Parameter
		$activation					= Input::get('activation');

		//1. get activation link
		$employee					= Employee::activationlink($activation_link)->first();

		if(!$employee)
		{
			$errors->add('Activation', 'Invalid activation link');
		}

		//2. validate activation
		$rules 			= 	[
								'password'	=> 'required|min:8|confirmed',
							];

		$validator 		= Validator::make($activation, $rules);

		if($validator->passes())
		{
			$employee->password 		= $activation['password'];
			$employee->activation_link 	= '';

			if(!$employee->save())
			{
				$errors->add('Activation', $employee->getError());
			}
		}
		else
		{
			$errors->add('Activation', $validator->errors());
		}
		
		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();

		return new JSend('success', ['employee' => $employee->toArray()]);
    }

}
