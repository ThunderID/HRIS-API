<?php

namespace App\ThunderID\WorkforceManagementV1\Http\Controllers;

use ThunderID\APIHelper\Data\JSend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Chart
 * 
 * @author cmooy
 */
class ScheduleController extends Controller
{
	/**
	 * Display all Charts
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index($org_id = null, $cal_id = 0)
	{
		$calendar 					= \App\ThunderID\WorkforceManagementV1\Models\Calendar::id($cal_id)->organisationid($org_id)->first();

		if(!$calendar)
		{
			\App::abort(404);
		}

		$result						= \App\ThunderID\WorkforceManagementV1\Models\Schedule::calendarid($cal_id);

		if($cal_id!=0)
		{
			$result 				= $result->calendarid($cal_id);
		}

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

		return new JSend('success', (array)['count' => $count, 'data' => $result, 'calendar' => $calendar->toArray()]);
	}

	/**
	 * Display a branch of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $cal_id = null, $id = null)
	{
		$result						= \App\ThunderID\WorkforceManagementV1\Models\Schedule::id($id)->calendarid($cal_id)->with(['schedule', 'branch'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store a Chart
	 * 1. store Chart
	 *
	 * @param Chart
	 * @return Response
	 */
	public function store($org_id = null, $cal_id = null)
	{
		//check branch
		$calendar 					= \App\ThunderID\WorkforceManagementV1\Models\Calendar::id($cal_id)->organisationid($org_id)->first();

		if(!$calendar)
		{
			\App::abort(404);
		}

		if(!Input::has('schedule'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data Chart.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate Chart Parameter
		$calendar						= Input::get('schedule');

		if(is_null($calendar['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$calendar_rules				=	[
											'calendar_id'					=> 'exists:hrwm_calendars,id|'.($is_new ? '' : 'in:'.$cal_id),
											'name'							=> 'max:255',
											'status'						=> 'in:DN,CB,UL,HB,L',
											'ondate'						=> 'date_format:"Y-m-d"',
											'start'							=> 'date_format:"H:i:s"',
											'end'							=> 'date_format:"H:i:s"',
											'break_idle'					=> 'numeric',
										];

		//1a. Get original data
		$calendar_data					= \App\ThunderID\WorkforceManagementV1\Models\Schedule::findornew($calendar['id']);

		//1b. Validate Basic Chart Parameter
		$validator					= Validator::make($calendar, $calendar_rules);

		if (!$validator->passes())
		{
			$errors->add('schedule', $validator->errors());
		}
		else
		{
			//if validator passed, save Chart
			$calendar['calendar_id']		= $cal_id;

			$calendar_data				= $calendar_data->fill($calendar);

			if(!$calendar_data->save())
			{
				$errors->add('schedule', $calendar_data->getError());
			}
		}
		//End of validate Chart

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_Chart				= \App\ThunderID\WorkforceManagementV1\Models\Schedule::id($calendar_data['id'])->first()->toArray();

		return new JSend('success', (array)$final_Chart);
	}

	/**
	 * Delete an Chart
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $cal_id = null, $id = null)
	{
		//check branch
		$calendar 					= \App\ThunderID\WorkforceManagementV1\Models\Calendar::id($cal_id)->organisationid($org_id)->first();

		if(!$calendar)
		{
			\App::abort(404);
		}

		$calendar						= \App\ThunderID\WorkforceManagementV1\Models\Schedule::calendarid($cal_id)->id($id)->first();

		if(!$calendar)
		{
			return new JSend('error', (array)Input::all(), 'Chart tidak ditemukan.');
		}

		$result						= $calendar->toArray();

		if($calendar->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $calendar->getError());
	}
}
