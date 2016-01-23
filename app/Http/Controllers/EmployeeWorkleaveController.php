<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of EmployeeWorkleave
 * 
 * @author cmooy
 */
class EmployeeWorkleaveController extends Controller
{
	/**
	 * Display all EmployeeWorkleaves
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index($org_id = null, $employ_id = null)
	{
		$employee 					= \App\Models\Employee::organisationid($org_id)->id($employ_id)->first();

		if(!$employee)
		{
			return new JSend('error', (array)Input::all(), 'Karyawan tidak valid.');
		}

		$result						= \App\Models\PersonWorkleave::personid($employ_id);

		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					default:
						# code...
						break;
				}
			}
		}

		$count						= $result->count();

		if(Input::has('skip'))
		{
			$skip					= Input::get('skip');
			$result					= $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take					= Input::get('take');
			$result					= $result->take($take);
		}

		$result						= $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}
}
