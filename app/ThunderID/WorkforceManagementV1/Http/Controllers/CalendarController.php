<?php

namespace App\ThunderID\WorkforceManagementV1\Http\Controllers;

use ThunderID\APIHelper\Data\JSend;
use App\Http\Controllers\Controller;
use App\Libraries\ValidatorOfCalendar as VOC;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of calendar
 * 
 * @author cmooy
 */
class CalendarController extends Controller
{
		/**
	 * Display all Organisations
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index($org_id)
	{
		$result						= new \App\ThunderID\WorkforceManagementV1\Models\Calendar;

		$result 					= $result->organisationid($org_id);

		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'name' :
						$result 		= $result->name($value);
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
					case 'name':
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
			$result->orderby('name', 'asc');
		}

		$count						= count($result->get());

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
	 * Display a calendar of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $id = null)
	{
		$result						= \App\ThunderID\WorkforceManagementV1\Models\Calendar::id($id)->organisationid($org_id)->with(['organisation'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * store a calendar of an org
	 *
	 * 1. Save calendar
	 * 2. Save Contacs
	 * @return Response
	 */
	public function store($org_id = null)
	{
		if(!Input::has('calendar'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data calendar.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate calendar Parameter
		$calendar					= Input::get('calendar');

		if(is_null($calendar['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$calendar_rules				=	[
											'organisation_id'	=> 'exists:hrom_organisations,id|'.($is_new ? '' : 'in:'.$org_id),
											'name'				=> 'max:255',
											'start'				=> 'date_format:"H:i:s"',
											'end'				=> 'date_format:"H:i:s"',
										];

		//1a. Get original data
		$calendar_data				= \App\ThunderID\WorkforceManagementV1\Models\Calendar::findornew($calendar['id']);

		//1b. Validate Basic calendar Parameter
		$validator					= Validator::make($calendar, $calendar_rules);

		if (!$validator->passes())
		{
			$errors->add('calendar', $validator->errors());
		}
		else
		{
			$validating_day 		= new VOC;

			if(!$validating_day->validate(['workdays' => explode(',', $calendar['workdays']), 'breaks' => explode(',', $calendar['break_idle'])]))
			{
				$errors->add('calendar', $validating_day->getError());
			}
			else
			{
				//if validator passed, save calendar
				$calendar_data['organisation_id']	= $org_id;
				$calendar_data						= $calendar_data->fill($calendar);

				if(!$calendar_data->save())
				{
					$errors->add('calendar', $calendar_data->getError());
				}
			}
		}
		//End of validate calendar

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_calendar			= \App\ThunderID\WorkforceManagementV1\Models\Calendar::id($calendar_data['id'])->organisationid($org_id)->with(['organisation'])->first()->toArray();

		return new JSend('success', (array)$final_calendar);
	}


	/**
	 * Delete a calendar
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $id = null)
	{
		//
		$calendar					= \App\ThunderID\WorkforceManagementV1\Models\Calendar::id($id)->organisationid($org_id)->with(['organisation'])->first();

		if(!$calendar)
		{
			return new JSend('error', (array)Input::all(), 'Kantor Cabang tidak ditemukan.');
		}

		$result					= $calendar->toArray();

		if($calendar->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $calendar->getError());
	}
}
