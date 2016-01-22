<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Schedule
 * 
 * @author cmooy
 */
class ScheduleController extends Controller
{
	/**
	 * Display all Schedules
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index($org_id = null, $cal_id = null)
	{
		$calendar 					= \App\Models\Calendar::organisationid($org_id)->id($cal_id)->first();

		if(!$calendar)
		{
			return new JSend('error', (array)Input::all(), 'Kalender tidak valid.');
		}

		$result						= \App\Models\Schedule::calendarid($cal_id);

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

	/**
	 * Display a schedule of a calendar
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $cal_id = null, $id = null)
	{
		$result						= \App\Models\Schedule::id($id)->calendarid($cal_id)->with(['calendar', 'calendar.calendars'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store a schedule
	 * 
	 * 1. case previous date
	 * 2. case next date in status CB/UL
	 * 3. case next date other status
	 * @return JSend Response
	 */
	public function store($org_id = null, $cal_id = null)
	{
		if(!Input::has('schedule'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data schedule.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate branch Parameter
		$branch						= Input::get('branch');

		if(is_null($branch['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$branch_rules				=	[
											'name'			=> 'required|max:255',
										];

		//1a. Get original data
		$branch_data				= \App\Models\Branch::findornew($branch['id']);

		//1b. Validate Basic branch Parameter
		$validator					= Validator::make($branch, $branch_rules);

		if (!$validator->passes())
		{
			$errors->add('Branch', $validator->errors());
		}
		else
		{
			//if validator passed, save branch
			$branch_data		= $branch_data->fill($branch);

			if(!$branch_data->save())
			{
				$errors->add('Branch', $branch_data->getError());
			}
		}
		//End of validate Branch

	}
}
