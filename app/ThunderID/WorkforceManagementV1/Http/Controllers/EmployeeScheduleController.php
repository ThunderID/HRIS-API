<?php

namespace App\ThunderID\WorkforceManagementV1\Http\Controllers;

use ThunderID\APIHelper\Data\JSend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Schdule
 * 
 * @author cmooy
 */
class EmployeeScheduleController extends Controller
{
	/**
	 * Display all Schdules
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index($org_id = null, $emp_id = 0)
	{
		$employee 					= \App\ThunderID\EmploymentSystemV1\Models\Employee::id($emp_id)->organisationid($org_id)->first();

		if(!$employee)
		{
			\App::abort(404);
		}

		$result						= \App\ThunderID\WorkforceManagementV1\Models\PersonSchedule::personid($emp_id);

		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'name' :
						$result 	= $result->name($value);
						break;
					case 'ondate' :
						$result 	= $result->ondate($value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		if(Input::has('sort'))
		{
			$sort                 = Input::get('sort');

			foreach ($sort as $key => $value) 
			{
				if(!in_array($value, ['asc', 'desc']))
				{
					return new JSend('error', (array)Input::all(), $key.' harus bernilai asc atau desc.');
				}
				switch (strtolower($key)) 
				{
					case 'ondate':
						$result     = $result->orderby($key, $value);
						break;
					default:
						# code...
						break;
				}
			}
		}
		else
		{
			$result     			= $result->orderby('ondate', 'asc');
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

		return new JSend('success', (array)['count' => $count, 'data' => $result, 'employee' => $employee->toArray()]);
	}

	/**
	 * Display a branch of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $emp_id = null, $id = null)
	{
		$result						= \App\ThunderID\WorkforceManagementV1\Models\PersonSchedule::id($id)->personid($emp_id)->with(['person'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store a Schdule
	 * 1. store Schdule
	 *
	 * @param Schdule
	 * @return Response
	 */
	public function store($org_id = null, $emp_id = null)
	{
		//check branch
		$schedule 					= \App\ThunderID\EmploymentSystemV1\Models\Employee::id($emp_id)->organisationid($org_id)->first();

		if(!$schedule)
		{
			\App::abort(404);
		}

		if(!Input::has('schedule'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data Schdule.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate Schdule Parameter
		$schedule						= Input::get('schedule');

		if(is_null($schedule['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$schedule_rules				=	[
											'person_id'						=> 'exists:hrps_persons,id|'.($is_new ? '' : 'in:'.$emp_id),
											'name'							=> 'max:255',
											'status'						=> 'in:DN,CB,UL,HB,L',
											'ondate'						=> 'date_format:"Y-m-d"',
											'start'							=> 'date_format:"H:i:s"',
											'end'							=> 'date_format:"H:i:s"',
											'break_idle'					=> 'numeric',
										];

		//1a. Get original data
		$schedule_data				= \App\ThunderID\WorkforceManagementV1\Models\PersonSchedule::findornew($schedule['id']);

		//1b. Validate Basic Schdule Parameter
		$validator					= Validator::make($schedule, $schedule_rules);

		if (!$validator->passes())
		{
			$errors->add('schedule', $validator->errors());
		}
		else
		{
			//if validator passed, save Schdule
			$schedule['person_id']	= $emp_id;

			$schedule_data			= $schedule_data->fill($schedule);

			if(!$schedule_data->save())
			{
				$errors->add('schedule', $schedule_data->getError());
			}
		}
		//End of validate Schdule

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_schedule				= \App\ThunderID\WorkforceManagementV1\Models\PersonSchedule::id($schedule_data['id'])->first()->toArray();

		return new JSend('success', (array)$final_schedule);
	}

	/**
	 * Delete an Schdule
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $emp_id = null, $id = null)
	{
		//check branch
		$schedule 					= \App\ThunderID\EmploymentSystemV1\Models\Employee::id($emp_id)->organisationid($org_id)->first();

		if(!$schedule)
		{
			\App::abort(404);
		}

		$schedule					= \App\ThunderID\WorkforceManagementV1\Models\PersonSchedule::personid($emp_id)->id($id)->first();

		if(!$schedule)
		{
			return new JSend('error', (array)Input::all(), 'Schdule tidak ditemukan.');
		}

		$result						= $schedule->toArray();

		if($schedule->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $schedule->getError());
	}
}
