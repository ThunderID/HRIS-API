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
		$calendar 					= \App\Models\Calendar::organisationid($org_id)->id($cal_id)->with(['calendars'])->first();

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

		$calendar['schedules']		= $result;

		return new JSend('success', (array)['count' => $count, 'data' => $calendar]);
	}
}
