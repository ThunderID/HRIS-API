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
	 * 1. store schedule of calendar to queue
	 * 2. store schedule of calendars to queue
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

		//1. store schedule of calendar to queue
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
											'calendar_id'					=> 'required|exists:tmp_calendars,id',
											'name'							=> 'required|max:255',
											'status'						=> 'required|in:DN,CB,UL,HB,L',
											'on'							=> 'required|date_format:"Y-m-d H:i:s"',
											'start'							=> 'required|date_format:"H:i:s"',
											'end'							=> 'required|date_format:"H:i:s"',
										];

		//1a. Validate Basic schedule Parameter
		$parameter 					= $schedule;
		unset($parameter['calendar']);

		$validator					= Validator::make($schedule, $schedule_rules);

		if (!$validator->passes())
		{
			$errors->add('Schedule', $validator->errors());
		}
		else
		{
			$total 						= \App\Models\Work::calendarid($cal_id)->count();

			$queue 						= new \App\Models\Queue;
			$queue->fill([
					'process_name' 			=> 'hr:schedules',
					'process_option' 		=> 'give',
					'parameter' 			=> json_encode($parameter),
					'total_process' 		=> $total,
					'task_per_process' 		=> 1,
					'process_number' 		=> 0,
					'total_task' 			=> $total,
					'message' 				=> 'Initial Commit',
				]);

			if(!$queue->save())
			{
				$errors->add('Schedule', $queue->getError());
			}
		}
		//End of validate schedule

		//2. store schedule of calendars to queue
		if(!$errors->count() && isset($schedule['calendar']['calendars']) && is_array($schedule['calendar']['calendars']))
		{
			foreach ($schedule['calendar']['calendars'] as $key => $value) 
			{
				$cals_data						= \App\Models\Calendar::id($value['id'])->calendarid($cal_id)->first();

				if(!$cals_data)
				{
					$errors->add('Calendar', 'Tidak ada kalender '.$value['name']);
				}

				if(!$errors->count())
				{
					$total 						= \App\Models\Work::calendarid($value['id'])->count();
					$parameter['calendar_id']	= $value['id'];

					$queue 						= new \App\Models\Queue;
					$queue->fill([
							'process_name' 			=> 'hr:schedules',
							'process_option' 		=> 'give',
							'parameter' 			=> json_encode($parameter),
							'total_process' 		=> $total,
							'task_per_process' 		=> 1,
							'process_number' 		=> 0,
							'total_task' 			=> $total,
							'message' 				=> 'Initial Commit',
						]);

					if(!$queue->save())
					{
						$errors->add('Schedule', $queue->getError());
					}
				}
			}
		}
		//End of validate calendar schedule

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		return new JSend('success', (array)Input::all());
	}
}
