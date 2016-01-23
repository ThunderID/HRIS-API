<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;

/**
 * Handle Protected Resource of EmployeeSchedule
 * 
 * @author cmooy
 */
class EmployeeScheduleController extends Controller
{
	/**
	 * Display all EmployeeSchedules
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

		if(!Input::has('search') || !isset(Input::get('search')['on']))
		{
			return new JSend('error', (array)Input::all(), 'Parameter on tidak tersedia.');
		}

		$on 						= Input::get('search')['on'];

		if(is_array($on))
		{
			$from					= Carbon::createFromFormat('Y-m-d', $on['0']);
			$to						= Carbon::createFromFormat('Y-m-d', $on['1']);
			$days 					= $from->diffInDays($to);

			$interval 				= new DateInterval('P1D');
			$to->add($interval);

			$daterange 				= new DatePeriod($from, $interval ,$to);

			$dates 					= [];
			foreach($daterange as $date)
			{
				$dates[] 				= $date->format('Y-m-d');
			}
		}
		else
		{
			$days 					= 1;
			$dates[] 				= Carbon::createFromFormat('Y-m-d', $on)->format('Y-m-d');
		}

		$result 					= [];
		$date_find 					= [];

		//1. check private schedule
		$pschedules 				= \App\Models\PersonSchedule::personid($employee['id'])->ondate($on)->get();

		if($pschedules->count())
		{
			foreach ($pschedules as $key => $value) 
			{
				$result[] 			= $value->toArray();
				$date_find[]		= $value['on']->format('Y-m-d');
			}
		}

		//2. check schedule
		$difference_date			= array_diff($dates, $date_find);
		
		if($difference_date)
		{
			$schedules 				= \App\Models\Schedule::calendarid($employee['current_calendar_id'])->whereIn('on', $difference_date)->get();
			if($schedules->count())
			{
				foreach ($schedules as $key => $value) 
				{
					$schedule 		= 	[
											'id'							=> '',
											'person_id'						=> $employ_id,
											'name'							=> $value['name'],
											'status'						=> $value['status'],
											'on'							=> $value['on']->format('Y-m-d H:i:s'),
											'start'							=> $value['start'],
											'end'							=> $value['end'],
											'break_idle'					=> $value['break_idle'],
										];

					$result[] 			= $schedule;
					$date_find[]		= $value['on']->format('Y-m-d');
				}
			}
		}

		//3. check calendar
		$difference_date			= array_diff($dates, $date_find);
		
		if($difference_date)
		{
			$calendar				= \App\Models\Calendar::findorfail($employee['current_calendar_id']);
			$harikerja 				= explode(',', $calendar['workdays']);
			$idles 					= explode(',', $calendar['break_idle']);

			$day 					= 	[
											'senin' => 'monday', 'selasa' => 'tuesday', 'rabu' => 'wednesday', 'kamis' => 'thursday', 'jumat' => 'friday', 'sabtu' => 'saturday', 'minggu' => 'sunday',
											'monday' => 'monday', 'tuesday' => 'tuesday', 'wednesday' => 'wednesday', 'thursday' => 'thursday', 'friday' => 'friday', 'saturday' => 'saturday', 'sunday' => 'sunday'
										];
			
			//translate to ing
			foreach ($harikerja as $key => $value) 
			{
				$workdays[]								= $day[strtolower($value)];
				$breakidles[$day[strtolower($value)]]	= $idles[$key];
			}

			foreach ($difference_date as $key => $value) 
			{
				$thatday			= Carbon::createFromFormat('Y-m-d', $value);
				
				if(in_array(strtolower($thatday->format('l')), $workdays))
				{
					$schedule 		= 	[
											'id'							=> '',
											'person_id'						=> $employ_id,
											'name'							=> 'Masuk Kantor',
											'status'						=> 'HB',
											'on'							=> $thatday->format('Y-m-d H:i:s'),
											'start'							=> $calendar['start'],
											'end'							=> $calendar['end'],
											'break_idle'					=> $breakidles[strtolower($thatday->format('l'))],
										];
				}
				else
				{
					$schedule 		= 	[
											'id'							=> '',
											'person_id'						=> $employ_id,
											'name'							=> 'Libur',
											'status'						=> 'L',
											'on'							=> $thatday->format('Y-m-d H:i:s'),
											'start'							=> '00:00:00',
											'end'							=> '00:00:00',
											'break_idle'					=> '0',
										];

				}

				$result[]			= $schedule;
			}
		}

		return new JSend('success', (array)['count' => $days, 'data' => $result]);
	}

	/**
	 * Store a schedule (throw to queue)
	 * 
	 * 1. store schedule of person to queue
	 * @return JSend Response
	 */
	public function store($org_id = null, $employ_id = null)
	{
		if(!Input::has('schedule'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data schedule.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. store schedule of person to queue
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
											'person_id'						=> 'required|exists:persons,id',
											'name'							=> 'required|max:255',
											'status'						=> 'required|in:DN,SS,SL,CN,CB,CI,UL,HB,L',
											'on'							=> 'required|date_format:"Y-m-d H:i:s"',
											'start'							=> 'required|date_format:"H:i:s"',
											'end'							=> 'required|date_format:"H:i:s"',
											'break_idle'					=> 'required|numeric',
										];

		//1a. Validate Basic schedule Parameter
		$parameter 					= $schedule;

		$validator					= Validator::make($parameter, $schedule_rules);

		if (!$validator->passes())
		{
			$errors->add('Schedule', $validator->errors());
		}
		else
		{
			$queue 						= new \App\Models\Queue;
			$queue->fill([
					'process_name' 			=> 'hr:personschedule',
					'process_option' 		=> 'store',
					'parameter' 			=> json_encode($parameter),
					'total_process' 		=> 1,
					'task_per_process' 		=> 1,
					'process_number' 		=> 0,
					'total_task' 			=> 1,
					'message' 				=> 'Initial Commit',
				]);

			if(!$queue->save())
			{
				$errors->add('Schedule', $queue->getError());
			}
		}
		//End of validate schedule

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		return new JSend('success', (array)Input::all());
	}

	/**
	 * Delete a schedule (throw to queue)
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $employ_id = null, $id = null)
	{
		//
		$schedule					= \App\Models\PersonSchedule::id($id)->personid($employ_id)->first();

		if(!$schedule)
		{
			return new JSend('error', (array)Input::all(), 'Jadwal tidak ditemukan.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. store schedule of calendar to queue
		$schedule					= $schedule->toArray();

		if(is_null($schedule['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$schedule_rules				=	[
											'person_id'						=> 'required|exists:persons,id',
											'name'							=> 'required|max:255',
											'status'						=> 'required|in:DN,CB,UL,HB,L',
											'on'							=> 'required|date_format:"Y-m-d H:i:s"',
											'start'							=> 'required|date_format:"H:i:s"',
											'end'							=> 'required|date_format:"H:i:s"',
											'break_idle'					=> 'required|numeric',
										];

		//1a. Validate Basic schedule Parameter
		$parameter 					= $schedule;

		$validator					= Validator::make($parameter, $schedule_rules);

		if (!$validator->passes())
		{
			$errors->add('Schedule', $validator->errors());
		}
		else
		{
			$queue 						= new \App\Models\Queue;
			$queue->fill([
					'process_name' 			=> 'hr:personschedule',
					'process_option' 		=> 'delete',
					'parameter' 			=> json_encode($parameter),
					'total_process' 		=> 1,
					'task_per_process' 		=> 1,
					'process_number' 		=> 0,
					'total_task' 			=> 1,
					'message' 				=> 'Initial Commit',
				]);

			if(!$queue->save())
			{
				$errors->add('Schedule', $queue->getError());
			}
		}
		//End of validate schedule

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		return new JSend('success', (array)$schedule);
	}
}
